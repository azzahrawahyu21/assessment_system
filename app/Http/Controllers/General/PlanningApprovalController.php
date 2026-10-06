<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\Planning;
use App\Models\PlanningApproval;
use App\Models\Department;
use App\Services\PlanningApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlanningApprovalController extends Controller
{
    protected array $approverRoles = ['kajur', 'wadir', 'direktur', 'keuangan'];

    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role?->name;

        if (! in_array($role, $this->approverRoles, true)) {
            abort(403);
        }

        $query = PlanningApproval::with([
                'planning.department',
                'planning.creator',
                'planning.planningType',
            ])
            ->where('approver_id', $user->id);

        // Filter status approval (pending / approved / revision)
        if ($request->filled('approval_status')) {
            $query->where('status', $request->approval_status);
        }

        if ($request->filled('search')) {
            $query->whereHas('planning', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('no_letter', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('year')) {
            $query->whereHas('planning', function ($q) use ($request) {
                $q->whereYear('date', $request->year);
            });
        }

        // Pending dulu, lalu terbaru
        $approvals = $query
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'revision')")
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $base = PlanningApproval::where('approver_id', $user->id);

        $stats = [
            'total'    => (clone $base)->count(),
            'pending'  => (clone $base)->where('status', 'pending')->count(),
            'approved' => (clone $base)->where('status', 'approved')->count(),
            'revision' => (clone $base)->where('status', 'revision')->count(),
        ];

        $departments = Department::orderBy('name')->get();
        $years = Planning::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('general.planning-approval.index', compact(
            'approvals', 'stats', 'departments', 'years', 'role'
        ));
    }

    public function show(PlanningApproval $planningApproval)
    {
        $user = Auth::user();

        if ((int) $planningApproval->approver_id !== (int) $user->id) {
            abort(403, 'Anda tidak berhak melihat data ini.');
        }

        $planningApproval->load([
            'planning.department',
            'planning.creator.role',
            'planning.planningType',
            'planning.approvals.approver.role',
        ]);

        $planning   = $planningApproval->planning;
        $myApproval = $planningApproval;
        $role       = $user->role?->name;
        $canAct     = $myApproval->status === 'pending';

        return view('general.planning-approval.show', compact(
            'planning', 'myApproval', 'role', 'canAct'
        ));
    }

    public function update(Request $request, PlanningApproval $planningApproval)
    {
        $user = Auth::user();

        if ((int) $planningApproval->approver_id !== (int) $user->id) {
            abort(403);
        }

        if ($planningApproval->status !== 'pending') {
            return back()->with('error', 'Approval ini sudah diproses.');
        }

        $role = $user->role?->name;
        $service = app(PlanningApprovalService::class);

        // Keuangan: hanya proses
        if ($role === 'keuangan') {
            $request->validate([
                'action' => 'required|in:process',
                'note'   => 'nullable|string|max:1000',
            ]);

            DB::transaction(function () use ($service, $planningApproval, $request) {
                $service->processFinance($planningApproval, $request->note);
            });

            return redirect()
                ->route('planning-approvals.index')
                ->with('success', 'Anggaran berhasil diproses. Perencanaan disetujui.');
        }

        // Kajur / Wadir / Direktur
        $validated = $request->validate([
            'status' => 'required|in:approved,revision',
            'note'   => 'required_if:status,revision|nullable|string|max:1000',
        ], [
            'note.required_if' => 'Catatan revisi wajib diisi.',
        ]);

        try {
            DB::transaction(function () use ($service, $planningApproval, $validated) {
                if ($validated['status'] === 'approved') {
                    $service->approve($planningApproval, $validated['note'] ?? null);
                } else {
                    $service->revise($planningApproval, $validated['note']);
                }
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        $msg = $validated['status'] === 'approved'
            ? 'Perencanaan disetujui dan diteruskan ke level berikutnya.'
            : 'Perencanaan dikembalikan untuk revisi.';

        return redirect()
            ->route('planning-approvals.index')
            ->with('success', $msg);
    }
}