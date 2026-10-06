<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Department;
use App\Models\Planning;
use App\Models\AssessmentJudgment;
use App\Models\AssessmentObjective;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Assessment::with(['department', 'creator'])
            ->where('created_by', Auth::id())
            ->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('department_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('department_id', $request->department_id)
                  ->orWhere('is_all_department', true);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assessments = $query->paginate(10)->withQueryString();
        $departments = Department::orderBy('name')->get();

        $base = Assessment::where('created_by', Auth::id());

        $stats = [
            'total'       => (clone $base)->count(),
            'draft'       => (clone $base)->where('status', 'draft')->count(),
            'in_progress' => (clone $base)->whereIn('status', ['design_factor_filled', 'in_progress'])->count(),
            'completed'   => (clone $base)->where('status', 'closed')->count(),
        ];

        return view('assessor.assessment.index', compact('assessments', 'departments', 'stats'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();

        // Jumlah planning yang sudah di-implementasi (per department & total)
        $implementedByDept = Planning::query()
            ->where('status', 'approved')
            ->whereHas('implementation')
            ->selectRaw('department_id, COUNT(*) as total')
            ->groupBy('department_id')
            ->pluck('total', 'department_id');

        $implementedTotal = Planning::query()
            ->where('status', 'approved')
            ->whereHas('implementation')
            ->count();

        return view('assessor.assessment.create', compact(
            'departments',
            'implementedByDept',
            'implementedTotal'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'assessment_type' => 'required|in:single,all',
            'department_id'   => 'required_if:assessment_type,single|nullable|exists:departments,id_department',
        ]);

        $isAll = $validated['assessment_type'] === 'all';

        // Pastikan ada planning yang sudah diimplementasi di scope yang dipilih
        $planningQuery = Planning::query()
            ->where('status', 'approved')
            ->whereHas('implementation');

        if (!$isAll) {
            $planningQuery->where('department_id', $validated['department_id']);
        }

        $count = $planningQuery->count();

        if ($count === 0) {
            return back()
                ->withInput()
                ->with('error', 'Tidak ada perencanaan yang sudah dilaksanakan pada scope yang dipilih.');
        }

        Assessment::create([
            'name'              => $validated['name'],
            'department_id'     => $isAll ? null : $validated['department_id'],
            'is_all_department' => $isAll,
            'created_by'        => Auth::id(),
            'status'            => 'draft',
        ]);

        return redirect()
            ->route('assessor.assessments.index')
            ->with('success', "Assessment berhasil dibuat. Scope mencakup {$count} perencanaan yang sudah dilaksanakan.");
    }

    public function show(Assessment $assessment)
    {
        abort_if($assessment->created_by !== Auth::id(), 403);

        $assessment->load(['department', 'creator']);

        // Planning yang relevan (sudah diimplementasi) sesuai scope assessment
        $planningsQuery = Planning::query()
            ->where('status', 'approved')
            ->whereHas('implementation')
            ->with(['department', 'planningType', 'implementation']);

        if (!$assessment->is_all_department) {
            $planningsQuery->where('department_id', $assessment->department_id);
        }

        $plannings = $planningsQuery->latest('id_plan')->get();

        return view('assessor.assessment.show', compact('assessment', 'plannings'));
    }

public function laporan(Assessment $assessment)
{
    abort_if($assessment->created_by !== Auth::id(), 403);

    $assessment->load(['department', 'creator']);

    $data = $this->buildLaporanData($assessment);

    return view('assessor.assessment.laporan', array_merge(
        ['assessment' => $assessment],
        $data
    ));
}

/**
 * Build data ringkasan untuk laporan
 */
private function buildLaporanData(Assessment $assessment): array
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

        $recommended = $judgment?->recommended_level !== null ? (int) $judgment->recommended_level : null;
        $achieved    = $judgment?->achieved_level !== null ? (int) $judgment->achieved_level : null;

        $gap = ($recommended !== null && $achieved !== null)
            ? max(0, $recommended - $achieved)
            : 0;

        if ($recommended !== null && $achieved !== null) {
            $totalAchieved    += $achieved;
            $totalRecommended += $recommended;
            $totalGap         += $gap;
            $countWithJudgment++;
        }

        // Hitung priority score (sama seperti di priorit y)
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
            'code'            => $code,
            'name'            => $name,
            'recommended'     => $recommended,
            'achieved'        => $achieved,
            'gap'             => $gap,
            'priority_label'  => $priorityLabel,
            'priority_score'  => $priorityScore,
            'notes'           => $judgment?->notes,
        ]);
    }

    // Urutkan berdasarkan priority score
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
        'institusi'          => 'Politeknik Negeri Madiun', // sesuaikan jika ada setting
    ];
}

/**
 * Download PDF Laporan
 */
public function laporanPdf(Assessment $assessment)
{
    abort_if($assessment->created_by !== Auth::id(), 403);

    $assessment->load(['department', 'creator']);
    $data = $this->buildLaporanData($assessment);

    // Menggunakan DomPDF (pastikan sudah install: composer require barryvdh/laravel-dompdf)
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('assessor.assessment.laporan-pdf', array_merge(
        ['assessment' => $assessment],
        $data
    ))->setPaper('a4', 'portrait');

    $filename = 'Laporan_Assessment_' . ($assessment->name ?? $assessment->id) . '_' . date('Ymd') . '.pdf';

    return $pdf->download($filename);
}

/**
 * Export Excel Laporan
 */
public function laporanExcel(Assessment $assessment)
{
    abort_if($assessment->created_by !== Auth::id(), 403);

    $assessment->load(['department', 'creator']);
    $data = $this->buildLaporanData($assessment);

    $filename = 'Laporan_Assessment_' . ($assessment->name ?? $assessment->id) . '_' . date('Ymd') . '.csv';

    $headers = [
        'Content-Type'        => 'text/csv; charset=UTF-8',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ];

    $callback = function () use ($assessment, $data) {
        $file = fopen('php://output', 'w');

        // BOM untuk Excel agar UTF-8 terbaca
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header info
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

        // Detail table
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
}