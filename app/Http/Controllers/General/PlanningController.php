<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\Planning;
use App\Models\PlanningType;
use App\Models\PlanningApproval;
use App\Models\Department;
use App\Models\User;
use App\Services\PlanningApprovalService;
use App\Services\DigitalSignatureService;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use setasign\Fpdi\Fpdi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PlanningController extends Controller
{
    /**
     * Hanya admin_prodi & upa yang boleh akses modul perencanaan.
     */
    protected function authorizePlanningAccess(): void
    {
        $role = Auth::user()->role?->name;

        if (! in_array($role, ['admin_prodi', 'upa'], true)) {
            abort(403, 'Anda tidak memiliki akses ke modul Perencanaan.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizePlanningAccess();

        $user  = Auth::user();
        $query = Planning::with(['planningType', 'department', 'creator']);

        // Hanya data milik user / unitnya
        $query->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('department_id', $user->department_id);
        });

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('no_letter', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', strtolower($request->status));
        }

        if ($request->filled('period')) {
            $query->where('period', strtolower($request->period));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        $plannings = $query->latest('id_plan')->paginate(10)->withQueryString();

        // Statistik (scope unit user)
        $base = Planning::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('department_id', $user->department_id);
        });

        $stats = [
            'total'     => (clone $base)->count(),
            'draft'     => (clone $base)->where('status', 'draft')->count(),
            'submitted' => (clone $base)->where('status', 'submitted')->count(),
            'revision'  => (clone $base)->where('status', 'revision')->count(),
            'approved'  => (clone $base)->where('status', 'approved')->count(),
        ];

        $years = Planning::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $departments = Department::orderBy('name')->get();

        return view('general.planning.index', compact(
            'plannings', 'stats', 'years', 'departments'
        ));
    }

    public function create()
    {
        $this->authorizePlanningAccess();

        $planningTypes = PlanningType::orderBy('name')->get();
        $departments   = Department::orderBy('name')->get();

        return view('general.planning.create', compact('planningTypes', 'departments'));
    }

    public function store(Request $request)
    {
        $this->authorizePlanningAccess();

        $validated = $request->validate([
            'no_letter'         => 'required|string|max:100|unique:plannings,no_letter',
            'name'              => 'required|string|max:255',
            'date'              => 'required|date',
            'period'            => 'required|in:ganjil,genap',
            'planning_type_id'  => 'required|exists:planning_types,id_type',
            'objective'         => 'nullable|string',
            'target'            => 'nullable|string',
            'success_indicator' => 'nullable|string',
            'budget'            => 'nullable|numeric|min:0',
            'funding_source'    => 'nullable|string|max:255',
            'budget_note'       => 'nullable|string',
            'document'          => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'department_id'     => 'nullable|exists:departments,id_department',
            'status'            => 'nullable|in:draft,submitted',
        ], [
            'no_letter.required' => 'Nomor surat wajib diisi.',
            'no_letter.unique'   => 'Nomor surat sudah digunakan.',
            'name.required'      => 'Nama perencanaan wajib diisi.',
            'date.required'      => 'Tanggal wajib diisi.',
            'period.required'    => 'Periode wajib dipilih.',
            'planning_type_id.required' => 'Jenis perencanaan wajib dipilih.',
        ]);

        // Bersihkan format ribuan dari input budget
        if ($request->filled('budget')) {
            $validated['budget'] = (float) preg_replace('/\D/', '', $request->budget);
        }

        $validated['created_by']    = Auth::id();
        $validated['department_id'] = $validated['department_id'] ?? Auth::user()->department_id;
        $validated['status']        = $request->input('status', 'draft');

        if ($request->hasFile('document')) {
            $validated['document_path'] = $request->file('document')->store('proposals', 'public');
        }

        $planning = Planning::create($validated);

        // Jika langsung submit
        if (($validated['status'] ?? '') === 'submitted') {
            try {
                app(PlanningApprovalService::class)->start($planning->fresh(['creator.role']));
            } catch (\Throwable $e) {
                return back()
                    ->withInput()
                    ->with('error', $e->getMessage());
            }
        }

        $msg = $validated['status'] === 'submitted'
            ? 'Perencanaan berhasil diajukan.'
            : 'Perencanaan berhasil disimpan sebagai draf.';

        return redirect()
            ->route('plannings.index')
            ->with('success', $msg);
    }

    public function show(Planning $planning)
    {
        $this->authorizePlanningAccess();
        $this->authorizeOwnership($planning);

        $planning->load([
            'planningType',
            'department',
            'creator',
            'approvals.approver.role',
            'implementations',
        ]);

        return view('general.planning.show', compact('planning'));
    }

    public function edit(Planning $planning)
    {
        $this->authorizePlanningAccess();
        $this->authorizeOwnership($planning);

        $status = strtolower(trim((string) $planning->status));

        // Hanya draft & revision yang boleh diedit
        // if (! in_array($status, ['draft', 'revision'], true)) {
        //     return redirect()
        //         ->route('plannings.show', $planning)
        //         ->with('error', 'Hanya perencanaan berstatus Draft atau Revisi yang dapat diedit.');
        // }

        $planningTypes = PlanningType::orderBy('name')->get();
        $departments   = Department::orderBy('name')->get();

        // Pastikan relasi ter-load untuk form
        $planning->load(['department', 'creator', 'planningType']);

        return view('general.planning.edit', compact('planning', 'planningTypes', 'departments'));
    }

    public function update(Request $request, Planning $planning)
    {
        $this->authorizePlanningAccess();
        $this->authorizeOwnership($planning);

        $status = strtolower(trim((string) $planning->status));

        // if (! in_array($status, ['draft', 'revision'], true)) {
        //     return redirect()
        //         ->route('plannings.show', $planning)
        //         ->with('error', 'Perencanaan tidak dapat diedit.');
        // }

        $validated = $request->validate([
            'no_letter'         => 'required|string|max:100|unique:plannings,no_letter,' . $planning->id_plan . ',id_plan',
            'name'              => 'required|string|max:255',
            'date'              => 'required|date',
            'period'            => 'required|in:ganjil,genap',
            'planning_type_id'  => 'required|exists:planning_types,id_type',
            'objective'         => 'nullable|string',
            'target'            => 'nullable|string',
            'success_indicator' => 'nullable|string',
            'budget'            => 'nullable|numeric|min:0',
            'funding_source'    => 'nullable|string|max:255',
            'budget_note'       => 'nullable|string',
            'document'          => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'status'            => 'nullable|in:draft,submitted',
        ]);

        // Budget: bersihkan format ribuan
        if ($request->filled('budget')) {
            $validated['budget'] = (float) preg_replace('/\D/', '', (string) $request->budget);
        }

        if ($request->hasFile('document')) {
            if ($planning->document_path) {
                Storage::disk('public')->delete($planning->document_path);
            }
            $validated['document_path'] = $request->file('document')->store('proposals', 'public');
        }

        // Status setelah simpan
        $newStatus = $request->input('status', 'draft');
        if ($status === 'revision' && $newStatus === 'draft') {
            $validated['status'] = 'draft';
        } else {
            $validated['status'] = $newStatus;
        }

        $planning->update($validated);

        if (($validated['status'] ?? '') === 'submitted') {
            app(PlanningApprovalService::class)->start($planning->fresh(['creator.role']));
        }

        return redirect()
            ->route('plannings.show', $planning)
            ->with('success', 'Perencanaan berhasil diperbarui.');
    }

    public function submit($id)
    {
        $this->authorizePlanningAccess();

        $planning = Planning::with('creator.role')->findOrFail($id);
        $this->authorizeOwnership($planning);

        $status = strtolower(trim((string) $planning->status));

        if (! in_array($status, ['draft', 'revision'], true)) {
            return back()->with('error', 'Status tidak valid untuk diajukan.');
        }

        try {
            app(PlanningApprovalService::class)->start($planning);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('plannings.show', $planning)
            ->with('success', 'Perencanaan berhasil diajukan.');
    }

    public function destroy(Planning $planning)
    {
        $this->authorizePlanningAccess();
        $this->authorizeOwnership($planning);

        if ($planning->status === 'approved') {
            return back()->with('error', 'Perencanaan yang sudah disetujui tidak dapat dihapus.');
        }

        if ($planning->document_path) {
            Storage::disk('public')->delete($planning->document_path);
        }

        $planning->delete();

        return redirect()
            ->route('plannings.index')
            ->with('success', 'Perencanaan berhasil dihapus.');
    }

    // ─── Helper ───────────────────────────────────────────────
    protected function authorizeOwnership(Planning $planning): void
    {
        $user = Auth::user();

        $allowed = (int) $planning->created_by === (int) $user->id
            || (
                $planning->department_id
                && $user->department_id
                && (int) $planning->department_id === (int) $user->department_id
            );

        if (! $allowed) {
            abort(403, 'Anda tidak berhak mengakses data ini.');
        }
    }

    protected function createKajurApproval(Planning $planning): void
    {
        app(PlanningApprovalService::class)
            ->start($planning->loadMissing('creator.role'));
    }

    public function proposal(Planning $planning)
    {
        $planning->load([
            'creator',
            'department',
            'planningType',
            'digitalSignature',
            'approvals.approver.role',   // pastikan relasi ini ada
        ]);

        $signature = $planning->digitalSignature;

        if (!$signature) {
            abort(404, 'Tanda tangan digital belum tersedia.');
        }

        $approvals = $planning->approvals
        ->where('status', 'approved');

        $wadirApproval = $approvals->first(function ($a) {
            return strtolower($a->approver->role->name ?? '') === 'wadir';
        });

        $direkturApproval = $approvals->first(function ($a) {
            return strtolower($a->approver->role->name ?? '') === 'direktur';
        });

        // Prioritas: nama user → fallback ke yang tersimpan di signature → default
        $wadirName = $wadirApproval?->approver?->name
            ?? $signature->wadir_name
            ?? 'Wakil Direktur 1';

        $directorName = $direkturApproval?->approver?->name
            ?? $signature->director_name
            ?? 'Direktur';

            // ===== Generate QR =====
        $qrBase64 = null;
        if (!empty($signature->token)) {
            $url = $signature->verification_url
                ?? route('signature.verify', $signature->token);

            $qrSvg = QrCode::format('svg')
                ->size(200)
                ->margin(1)
                ->generate($url);

            // Simpan sebagai data URI SVG
            $qrBase64 = base64_encode($qrSvg);
        }

        // ===== 1. Generate proposal DomPDF =====
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('general.digital-signatures.pdf', [
            'signature' => $signature,
            'planning'  => $planning,
            'qrBase64'  => $qrBase64,
            'wadirName'    => $wadirName,      // kirim ke view
            'directorName' => $directorName,   // kirim ke view
        ])->setPaper('a4', 'portrait');

        $proposalContent = $pdf->output();
        $safeFilename = str_replace(['/', '\\'], '-', $signature->document_number ?? $planning->no_letter);

        // ===== 2. Cari file PDF lampiran =====
        $docPath = null;
        if ($planning->document_path) {
            $fullPath = storage_path('app/public/' . ltrim($planning->document_path, '/'));
            if (!is_file($fullPath)) {
                $fullPath = storage_path('app/' . ltrim($planning->document_path, '/'));
            }
            if (is_file($fullPath) && strtolower(pathinfo($fullPath, PATHINFO_EXTENSION)) === 'pdf') {
                $docPath = $fullPath;
            }
        }

        if (!$docPath) {
            return response($proposalContent, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Surat-'.$safeFilename.'.pdf"',
            ]);
        }

        // ===== 3. Merge =====
        $tempProposal = tempnam(sys_get_temp_dir(), 'proposal_') . '.pdf';
        file_put_contents($tempProposal, $proposalContent);

        try {
            $merger = new Fpdi();

            $pageCount = $merger->setSourceFile($tempProposal);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $merger->importPage($i);
                $size = $merger->getTemplateSize($tpl);
                $merger->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $merger->useTemplate($tpl);
            }

            $pageCount = $merger->setSourceFile($docPath);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $merger->importPage($i);
                $size = $merger->getTemplateSize($tpl);
                $merger->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $merger->useTemplate($tpl);
            }

            $merged = $merger->Output('S');

            return response($merged, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Surat-'.$safeFilename.'.pdf"',
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response($proposalContent, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Surat-'.$safeFilename.'.pdf"',
            ]);
        } finally {
            if (is_file($tempProposal)) {
                @unlink($tempProposal);
            }
        }
    }
}