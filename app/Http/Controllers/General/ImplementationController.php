<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\Planning;
use App\Models\Implementation;
use App\Models\ImplementationEvidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ImplementationController extends Controller
{
    public function index(Request $request)
    {
        $query = Planning::query()
            ->where('status', 'approved')
            ->with(['department', 'planningType', 'implementation.evidences'])
            ->latest('id_plan');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('no_letter', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('fill_status')) {
            if ($request->fill_status === 'empty') {
                $query->whereDoesntHave('implementation');
            } elseif ($request->fill_status === 'filled') {
                $query->whereHas('implementation');
            }
        }

        $plannings = $query->paginate(10)->withQueryString();

        $stats = [
            'total'  => Planning::where('status', 'approved')->count(),
            'empty'  => Planning::where('status', 'approved')->whereDoesntHave('implementation')->count(),
            'filled' => Planning::where('status', 'approved')->whereHas('implementation')->count(),
        ];

        return view('general.implementation.index', compact('plannings', 'stats'));
    }

    public function create(Planning $planning)
    {
        if (strtolower($planning->status) !== 'approved') {
            return redirect()
                ->route('implementations.index')
                ->with('error', 'Hanya perencanaan yang sudah disetujui yang dapat dilaksanakan.');
        }

        if ($planning->implementation) {
            return redirect()
                ->route('implementations.edit', $planning->implementation)
                ->with('info', 'Pelaksanaan sudah ada. Anda dapat mengeditnya.');
        }

        $planning->load(['department', 'planningType']);

        return view('general.implementation.create', compact('planning'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'planning_id' => 'required|exists:plannings,id_plan',
            'date'        => 'required|date',
            'result'      => 'nullable|string',
            'obstacle'    => 'nullable|string',
            'evidences'   => 'nullable|array',
            'evidences.*.file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,mp4|max:10240',
            'evidences.*.type' => 'nullable|string|max:50',
        ]);

        $planning = Planning::findOrFail($request->planning_id);

        if (strtolower($planning->status) !== 'approved') {
            return back()->with('error', 'Hanya perencanaan approved yang bisa diimplementasikan.');
        }

        if ($planning->implementation) {
            return back()->with('error', 'Pelaksanaan untuk perencanaan ini sudah ada.');
        }

        DB::transaction(function () use ($request) {
            $impl = Implementation::create([
                'planning_id' => $request->planning_id,
                'date'        => $request->date,
                'result'      => $request->result,
                'obstacle'    => $request->obstacle,
                'created_by'  => Auth::id(),
            ]);

            if ($request->has('evidences')) {
                foreach ($request->evidences as $row) {
                    if (empty($row['file']) || !$row['file']->isValid()) {
                        continue;
                    }
                    $file = $row['file'];
                    $path = $file->store('evidences', 'public');

                    ImplementationEvidence::create([
                        'implementation_id' => $impl->id,
                        'file_path'         => $path,
                        'file_name'         => $file->getClientOriginalName(),
                        'file_type'         => $row['type'] ?? $file->getClientMimeType(),
                    ]);
                }
            }
        });

        return redirect()
            ->route('implementations.index')
            ->with('success', 'Pelaksanaan berhasil disimpan.');
    }

    public function show(Implementation $implementation)
    {
        $implementation->load(['planning.department', 'planning.planningType', 'creator', 'evidences']);

        return view('general.implementation.show', compact('implementation'));
    }

    public function edit(Implementation $implementation)
    {
        $implementation->load(['planning.department', 'planning.planningType', 'evidences']);

        return view('general.implementation.edit', compact('implementation'));
    }

    public function update(Request $request, Implementation $implementation)
    {
        $request->validate([
            'date'      => 'required|date',
            'result'    => 'nullable|string',
            'obstacle'  => 'nullable|string',
            'evidences' => 'nullable|array',
            'evidences.*.file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,mp4|max:10240',
            'evidences.*.type' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request, $implementation) {
            $implementation->update($request->only(['date', 'result', 'obstacle']));

            if ($request->has('evidences')) {
                foreach ($request->evidences as $row) {
                    if (empty($row['file']) || !$row['file']->isValid()) {
                        continue;
                    }
                    $file = $row['file'];
                    $path = $file->store('evidences', 'public');

                    ImplementationEvidence::create([
                        'implementation_id' => $implementation->id,
                        'file_path'         => $path,
                        'file_name'         => $file->getClientOriginalName(),
                        'file_type'         => $row['type'] ?? $file->getClientMimeType(),
                    ]);
                }
            }
        });

        return redirect()
            ->route('implementations.index')
            ->with('success', 'Pelaksanaan berhasil diperbarui.');
    }

    public function destroyEvidence(ImplementationEvidence $evidence)
    {
        Storage::disk('public')->delete($evidence->file_path);
        $evidence->delete();

        return back()->with('success', 'Bukti berhasil dihapus.');
    }

    public function destroy(Implementation $implementation)
    {
        $implementation->load('evidences');

        foreach ($implementation->evidences as $evidence) {
            Storage::disk('public')->delete($evidence->file_path);
        }

        $implementation->delete();

        return redirect()
            ->route('implementations.index')
            ->with('success', 'Pelaksanaan berhasil dihapus.');
    }
}