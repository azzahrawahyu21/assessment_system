<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentJudgment;
use App\Models\AssessmentObjective;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultAssessmentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'load')) {
            $user->load('role', 'department');
        }

        $role = strtolower($user->role?->name ?? '');

        // Role yang hanya melihat assessment department sendiri
        $departmentScoped = in_array($role, [
            'admin_prodi', 'prodi', 'upa', 'kaprodi', 'dosen'
        ]);

        // Role yang melihat semua assessment (se-kampus)
        $campusWide = in_array($role, [
            'kajur', 'wadir', 'direktur', 'administrator', 'admin'
        ]);

        $query = Assessment::with('department')
            ->whereHas('assessmentObjectives') // hanya yang sudah ada objectives
            ->latest();

        if ($departmentScoped && $user->department_id) {
            // Tampilkan assessment department sendiri ATAU yang is_all_department
            $query->where(function ($q) use ($user) {
                $q->where('department_id', $user->department_id)
                  ->orWhere('is_all_department', true);
            });
        } elseif (!$campusWide && !$departmentScoped) {
            $query->whereRaw('1 = 0');
        }

        $assessments = $query->get();

        $data = [
            'assessments' => $assessments,
            'assessment'  => null,
            'domains'     => collect(),
            'chartData'   => [],
            'items'       => collect(),
        ];

        if ($request->filled('assessment_id')) {
            $assessment = Assessment::with('department')
                ->findOrFail($request->assessment_id);

            // Security check
            if ($departmentScoped) {
                $allowed = $assessment->department_id === $user->department_id
                    || ($assessment->is_all_department ?? false);

                if (!$allowed) {
                    abort(403, 'Anda tidak memiliki akses ke assessment ini.');
                }
            }

            // ===== Gap Analysis =====
            $domains = $this->getGapDomains($assessment);

            $totalDomains = $domains->count();
            $gapClosed    = $domains->where('gap', 0)->count();
            $gapOpen      = $domains->filter(fn ($d) => $d->gap !== null && $d->gap != 0)->count();
            $avgGap       = $totalDomains > 0 ? round($domains->avg('gap') ?? 0, 2) : 0;

            $chartData = $domains->map(fn ($d) => [
                'code'              => $d->code ?? '-',
                'name'              => $d->name ?? $d->description ?? '-',
                'recommended_level' => $d->recommended_level !== null ? (int) $d->recommended_level : null,
                'assessor_level'    => $d->assessor_level !== null ? (int) $d->assessor_level : null,
                'gap'               => $d->gap !== null ? (int) $d->gap : null,
            ])->values()->toArray();

            // ===== Laporan =====
            $laporan = $this->getLaporanData($assessment);

            $data = array_merge($data, [
                'assessment'         => $assessment,
                'domains'            => $domains,
                'totalDomains'       => $totalDomains,
                'gapClosed'          => $gapClosed,
                'gapOpen'            => $gapOpen,
                'avgGap'             => $avgGap,
                'chartData'          => $chartData,
                'currentCapability'  => $laporan['currentCapability'] ?? 0,
                'targetCapability'   => $laporan['targetCapability'] ?? 0,
                'averageGap'         => $laporan['averageGap'] ?? 0,
                'objectivesAssessed' => $laporan['objectivesAssessed'] ?? 0,
                'veryHighPriority'   => $laporan['veryHighPriority'] ?? 0,
                'highPriority'       => $laporan['highPriority'] ?? 0,
                'mediumPriority'     => $laporan['mediumPriority'] ?? 0,
                'items'              => $laporan['items'] ?? collect(),
                'year'               => $laporan['year'] ?? ($assessment->created_at?->format('Y') ?? date('Y')),
                'institusi'          => $laporan['institusi'] ?? 'Politeknik Negeri Madiun',
            ]);
        }

        return view('general.result-assessment.index', $data);
    }

    /**
     * Ambil data domain + gap (sama seperti CapabilityController@gap)
     */
    protected function getGapDomains(Assessment $assessment)
    {
        $judgments = AssessmentJudgment::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('cobit_id');

        $objectives = AssessmentObjective::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->orderByDesc('score')
            ->get();

        return $objectives->map(function ($obj) use ($judgments) {
            $judgment = $judgments->get($obj->cobit_id);

            $recommended = $judgment?->recommended_level !== null
                ? (int) $judgment->recommended_level
                : null;

            $achieved = $judgment?->achieved_level !== null
                ? (int) $judgment->achieved_level
                : null;

            // Gap = Assessor − Rekomendasi (sama seperti di gap-analysis/show)
            $gap = ($recommended !== null && $achieved !== null)
                ? $achieved - $recommended
                : null;

            $cobit = $obj->cobit;

            return (object) [
                'id'                => $obj->id,
                'cobit_id'          => $obj->cobit_id,
                'code'              => $cobit->code
                                        ?? $cobit->process_id
                                        ?? $cobit->process_code
                                        ?? '-',
                'name'              => $cobit->process_name
                                        ?? $cobit->name
                                        ?? $cobit->title
                                        ?? $cobit->process_title
                                        ?? $cobit->description
                                        ?? '-',
                'description'       => $cobit->description ?? null,
                'recommended_level' => $recommended,
                'assessor_level'    => $achieved,
                'achieved_level'    => $achieved,
                'gap'               => $gap,
                'notes'             => $judgment?->notes,
                'has_judgment'      => $judgment !== null,
            ];
        });
    }

    /**
     * Ambil data laporan (sama seperti AssessmentController@buildLaporanData)
     */
    protected function getLaporanData(Assessment $assessment): array
    {
        $judgments = AssessmentJudgment::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('cobit_id');

        $objectives = AssessmentObjective::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->orderByDesc('score')
            ->get();

        $items = collect();
        $totalAchieved = 0;
        $totalRecommended = 0;
        $totalGap = 0;
        $countWithJudgment = 0;

        $veryHigh = 0;
        $high = 0;
        $medium = 0;
        $low = 0;

        foreach ($objectives as $obj) {
            $judgment = $judgments->get($obj->cobit_id);

            $recommended = $judgment?->recommended_level !== null
                ? (int) $judgment->recommended_level
                : null;

            $achieved = $judgment?->achieved_level !== null
                ? (int) $judgment->achieved_level
                : null;

            // Untuk laporan, gap dihitung sebagai selisih yang masih kurang (max 0)
            $gap = ($recommended !== null && $achieved !== null)
                ? max(0, $recommended - $achieved)
                : 0;

            if ($recommended !== null && $achieved !== null) {
                $totalAchieved    += $achieved;
                $totalRecommended += $recommended;
                $totalGap         += $gap;
                $countWithJudgment++;
            }

            $score  = (float) ($obj->score ?? 0);
            $impact = min(5, max(3, (int) ceil($score / 20) + 2));
            $risk   = min(5, max(3, $gap > 0 ? (3 + $gap) : 3));
            $priorityScore = $gap * $risk * $impact;

            if ($priorityScore >= 60) {
                $veryHigh++;
                $priorityLabel = 'VERY HIGH';
            } elseif ($priorityScore >= 35) {
                $high++;
                $priorityLabel = 'HIGH';
            } elseif ($priorityScore >= 15) {
                $medium++;
                $priorityLabel = 'MEDIUM';
            } else {
                $low++;
                $priorityLabel = $priorityScore > 0 ? 'LOW' : 'NONE';
            }

            $cobit = $obj->cobit;
            $code = $cobit->code ?? $cobit->process_id ?? $cobit->process_code ?? '-';
            $name = $cobit->process_name
                ?? $cobit->name
                ?? $cobit->title
                ?? $cobit->process_title
                ?? $cobit->description
                ?? '—';

            $items->push((object) [
                'code'           => $code,
                'name'           => $name,
                'recommended'    => $recommended,
                'achieved'       => $achieved,
                'gap'            => $gap,
                'priority_label' => $priorityLabel,
                'priority_score' => $priorityScore,
                'notes'          => $judgment?->notes,
            ]);
        }

        $items = $items->sortByDesc('priority_score')->values();

        $currentCapability = $countWithJudgment > 0
            ? round($totalAchieved / $countWithJudgment, 2)
            : 0;

        $targetCapability = $countWithJudgment > 0
            ? round($totalRecommended / $countWithJudgment, 2)
            : 0;

        $averageGap = $countWithJudgment > 0
            ? round($totalGap / $countWithJudgment, 2)
            : 0;

        return [
            'items'              => $items,
            'currentCapability'  => $currentCapability,
            'targetCapability'   => $targetCapability,
            'averageGap'         => $averageGap,
            'objectivesAssessed' => $objectives->count(),
            'veryHighPriority'   => $veryHigh,
            'highPriority'       => $high,
            'mediumPriority'     => $medium,
            'lowPriority'        => $low,
            'year'               => $assessment->created_at?->format('Y') ?? date('Y'),
            'institusi'          => 'Politeknik Negeri Madiun',
        ];
    }

    /**
     * Download PDF Laporan (untuk role yang berhak melihat hasil)
     */
    public function laporanPdf(Assessment $assessment)
    {
        $this->authorizeView($assessment);

        $assessment->load(['department', 'creator']);
        $data = $this->getLaporanData($assessment);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'assessor.assessment.laporan-pdf',
            array_merge(['assessment' => $assessment], $data)
        )->setPaper('a4', 'portrait');

        $filename = 'Laporan_Assessment_' . ($assessment->name ?? $assessment->id) . '_' . date('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export Excel Laporan (untuk role yang berhak melihat hasil)
     */
    public function laporanExcel(Assessment $assessment)
    {
        $this->authorizeView($assessment);

        $assessment->load(['department', 'creator']);
        $data = $this->getLaporanData($assessment);

        $filename = 'Laporan_Assessment_' . ($assessment->name ?? $assessment->id) . '_' . date('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($assessment, $data) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 agar Excel terbaca benar
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['LAPORAN ASSESSMENT COBIT 2019']);
            fputcsv($file, []);
            fputcsv($file, ['Institusi', $data['institusi']]);
            fputcsv($file, ['Tahun Assessment', $data['year']]);
            fputcsv($file, ['Nama Assessment', $assessment->name ?? '-']);
            fputcsv($file, []);
            fputcsv($file, ['Current Capability', $data['currentCapability']]);
            fputcsv($file, ['Target Capability', $data['targetCapability']]);
            fputcsv($file, ['Average Gap', $data['averageGap']]);
            fputcsv($file, []);
            fputcsv($file, ['Objectives Assessed', $data['objectivesAssessed']]);
            fputcsv($file, ['Very High Priority', $data['veryHighPriority']]);
            fputcsv($file, ['High Priority', $data['highPriority']]);
            fputcsv($file, ['Medium Priority', $data['mediumPriority']]);
            fputcsv($file, []);
            fputcsv($file, []);
            fputcsv($file, ['Kode', 'Nama Domain', 'Recommended', 'Achieved', 'Gap', 'Priority', 'Catatan']);

            foreach ($data['items'] as $item) {
                fputcsv($file, [
                    $item->code,
                    $item->name,
                    $item->recommended ?? '-',
                    $item->achieved ?? '-',
                    $item->gap,
                    $item->priority_label,
                    $item->notes ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Cek apakah user berhak melihat assessment ini
     */
    protected function authorizeView(Assessment $assessment): void
    {
        $user = Auth::user();
        if (method_exists($user, 'load')) {
            $user->load('role', 'department');
        }

        $role = strtolower($user->role?->name ?? '');

        $departmentScoped = in_array($role, [
            'admin_prodi', 'prodi', 'upa', 'kaprodi', 'dosen'
        ]);

        $campusWide = in_array($role, [
            'kajur', 'wadir', 'direktur', 'administrator', 'admin'
        ]);

        if ($campusWide) {
            return; // boleh semua
        }

        if ($departmentScoped) {
            $allowed = $assessment->department_id === $user->department_id
                || ($assessment->is_all_department ?? false);

            if ($allowed) {
                return;
            }
        }

        abort(403, 'Anda tidak memiliki akses ke laporan assessment ini.');
    }
}