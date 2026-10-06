<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\PlanningType;
use Illuminate\Http\Request;

class PlanningTypeController extends Controller
{
    // public function index()
    // {
    //     $types = PlanningType::withCount('plannings')
    //         ->orderBy('name')
    //         ->get();

    //     $stats = [
    //         'total' => $types->count(),
    //         'used'  => $types->where('plannings_count', '>', 0)->count(),
    //     ];

    //     return view('general.planning-type.index', compact('types', 'stats'));
    // }
    public function index(Request $request)
    {
        $types = PlanningType::withCount('plannings')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => PlanningType::count(),
            'used'  => PlanningType::has('plannings')->count(),
        ];

        return view('general.planning-type.index', compact('types', 'stats'));
    }

    public function create()
    {
        return view('general.planning-type.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:planning_types,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama jenis perencanaan wajib diisi.',
            'name.unique'   => 'Nama jenis perencanaan sudah digunakan.',
        ]);

        PlanningType::create($validated);

        return redirect()
            ->route('planning-types.index')
            ->with('success', 'Jenis perencanaan berhasil ditambahkan.');
    }

    public function show(PlanningType $planningType)
    {
        $planningType->loadCount('plannings');

        return view('general.planning-type.show', compact('planningType'));
    }

    public function edit(PlanningType $planningType)
    {
        return view('general.planning-type.edit', compact('planningType'));
    }

    public function update(Request $request, PlanningType $planningType)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:planning_types,name,' . $planningType->id_type . ',id_type',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama jenis perencanaan wajib diisi.',
            'name.unique'   => 'Nama jenis perencanaan sudah digunakan.',
        ]);

        $planningType->update($validated);

        return redirect()
            ->route('planning-types.index')
            ->with('success', 'Jenis perencanaan berhasil diperbarui.');
    }

    public function destroy(PlanningType $planningType)
    {
        // Cegah hapus jika sudah dipakai di perencanaan
        if ($planningType->plannings()->exists()) {
            return back()->with('error', 'Jenis perencanaan tidak dapat dihapus karena sudah digunakan.');
        }

        $planningType->delete();

        return redirect()
            ->route('planning-types.index')
            ->with('success', 'Jenis perencanaan berhasil dihapus.');
    }
}