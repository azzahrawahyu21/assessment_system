<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\DigitalSignature;
use App\Models\Planning;
use App\Services\DigitalSignatureService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DigitalSignatureController extends Controller
{
    public function __construct(
        protected DigitalSignatureService $signatureService
    ) {}

    public function index()
    {
        $signatures = DigitalSignature::with(['planning.creator', 'planning.department', 'generatedBy'])
            ->latest()
            ->paginate(15);

        return view('general.digital-signatures.index', compact('signatures'));
    }

    public function create()
    {
        $plannings = Planning::whereDoesntHave('digitalSignature')
            ->where('status', 'approved') // lowercase
            ->with(['creator', 'department'])
            ->latest('id_plan')
            ->get();

        return view('general.digital-signatures.create', compact('plannings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'planning_id' => 'required|exists:plannings,id_plan',
        ]);

        $planning = Planning::findOrFail($request->planning_id);

        try {
            $signature = $this->signatureService->generate($planning, $request->user());

            return redirect()
                ->route('general.digital-signatures.show', $signature)
                ->with('success', 'Tanda tangan digital berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(DigitalSignature $digital_signature)
    {
        $digital_signature->load(['planning.creator', 'planning.department', 'generatedBy']);

        return view('general.digital-signatures.show', [
            'digitalSignature' => $digital_signature
        ]);
    }

    public function pdf(Planning $planning)
    {
        $signature = $planning->digitalSignature;

        if (!$signature) {
            abort(404, 'Tanda tangan digital belum tersedia.');
        }

        $planning->load(['creator', 'department', 'planningType']);

        $qrBase64 = null;
        // isi generate QR di sini kalau sudah ada package-nya

        // 1. Generate proposal DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('general.digital-signatures.pdf', [
            'signature' => $signature,
            'planning'  => $planning,
            'qrBase64'  => $qrBase64,
        ])->setPaper('a4', 'portrait');

        $proposalContent = $pdf->output();
        $safeFilename = str_replace(['/', '\\'], '-', $signature->document_number);

        // 2. Cek lampiran PDF
        $docPath = null;
        if ($planning->document_path) {
            $fullPath = storage_path('app/public/' . ltrim($planning->document_path, '/'));
            if (is_file($fullPath) && strtolower(pathinfo($fullPath, PATHINFO_EXTENSION)) === 'pdf') {
                $docPath = $fullPath;
            }
        }

        // Tidak ada lampiran PDF → langsung return proposal
        if (!$docPath) {
            return response($proposalContent, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Surat-'.$safeFilename.'.pdf"',
            ]);
        }

        // 3. Merge
        $tempProposal = tempnam(sys_get_temp_dir(), 'proposal_') . '.pdf';
        file_put_contents($tempProposal, $proposalContent);

        try {
            $merger = new \setasign\Fpdi\Fpdi();

            // Proposal
            $pageCount = $merger->setSourceFile($tempProposal);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $merger->importPage($i);
                $size = $merger->getTemplateSize($tpl);
                $merger->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $merger->useTemplate($tpl);
            }

            // Lampiran
            $pageCount = $merger->setSourceFile($docPath);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $merger->importPage($i);
                $size = $merger->getTemplateSize($tpl);
                $merger->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $merger->useTemplate($tpl);
            }

            $mergedContent = $merger->Output('S');

            return response($mergedContent, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Surat-'.$safeFilename.'.pdf"',
            ]);
        } catch (\Throwable $e) {
            // Fallback: kalau merge gagal (misal PDF lampiran terlalu baru), kirim proposal saja
            report($e); // biar masuk log

            return response($proposalContent, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Surat-'.$safeFilename.'.pdf"',
            ]);
        } finally {
            if (isset($tempProposal) && is_file($tempProposal)) {
                @unlink($tempProposal);
            }
        }
    }

    public function destroy(DigitalSignature $digital_signature)
    {
        $digital_signature->update(['status' => 'revoked']);

        return redirect()
            ->route('general.digital-signatures.index')
            ->with('success', 'Tanda tangan digital berhasil dicabut.');
    }
}