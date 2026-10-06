<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentObjective;
use App\Models\AssessmentRespondent;
use App\Models\AssessmentResult;
use App\Models\User;
use App\Models\AssessmentJudgment;
use App\Services\CapabilityCalculator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CapabilityController extends Controller
{
    public function index()
    {
        // $assessments = Assessment::with(['department'])
        //     ->withCount(['assessmentObjectives as objective_priorities_count'])
        //     ->withCount(['respondents as respondents_count'])
        //     ->withCount(['respondents as completed_respondents_count' => fn ($q) => $q->where('status', 'completed')])
        //     ->where('created_by', Auth::id())
        //     ->whereHas('assessmentObjectives')
        //     ->latest()
        //     ->get()
        //     ->map(function ($a) {
        //         $a->is_all_departments = (bool) ($a->is_all_department ?? false);
        //         return $a;
        //     });
        $assessments = Assessment::with(['department'])
            ->withCount(['assessmentObjectives as objective_priorities_count'])
            ->withCount(['respondents as respondents_count'])
            ->withCount(['respondents as completed_respondents_count' => fn ($q) => $q->where('status', 'completed')])
            ->where('created_by', Auth::id())
            ->whereHas('assessmentObjectives')
            ->latest()
            ->paginate(10);

        $assessments->getCollection()->transform(function ($a) {
            $a->is_all_departments = (bool) ($a->is_all_department ?? false);
            return $a;
        });

        return view('assessor.capability.index', compact('assessments'));
    }

    public function start(Assessment $assessment)
    {
        abort_if($assessment->created_by !== Auth::id(), 403);

        if (!$assessment->assessmentObjectives()->exists()) {
            return redirect()
                ->route('assessor.objectives.index')
                ->with('error', 'Generate Objectives terlebih dahulu.');
        }

        $query = User::whereHas('role', fn ($q) => $q->whereIn('name', ['dosen', 'kaprodi', 'admin_prodi']));

        if (!($assessment->is_all_department ?? false) && $assessment->department_id) {
            $query->where('department_id', $assessment->department_id);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            return back()->with('error', 'Tidak ada responden (dosen/kaprodi/admin_prodi) di department ini.');
        }

        DB::transaction(function () use ($assessment, $users) {
            foreach ($users as $user) {
                AssessmentRespondent::firstOrCreate(
                    ['assessment_id' => $assessment->id, 'user_id' => $user->id],
                    ['status' => 'pending']
                );
            }
        });

        return redirect()
            ->route('assessor.capability.show', $assessment)
            ->with('success', 'Capability Assessment dimulai. ' . $users->count() . ' responden ditugaskan.');
    }

    /**
     * Daftar domain berdasarkan objectives assessment
     */
    public function show(Assessment $assessment)
    {
        abort_if($assessment->created_by !== Auth::id(), 403);

        $assessment->load('department');

        $objectives = AssessmentObjective::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->orderByDesc('score')
            ->get();

        if ($objectives->isEmpty()) {
            return redirect()
                ->route('assessor.capability.index')
                ->with('error', 'Belum ada objectives. Generate dulu.');
        }

        $respondents = AssessmentRespondent::with('user.role')
            ->where('assessment_id', $assessment->id)
            ->get();

        return view('assessor.capability.show', compact(
            'assessment',
            'objectives',
            'respondents'
        ));
    }

    /**
     * Hasil capability (aggregate)
     */
    public function result(Assessment $assessment)
    {
        abort_if($assessment->created_by !== Auth::id(), 403);

        $assessment->load('department');

        $objectives = AssessmentObjective::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->orderByDesc('score')
            ->get();

        $results = AssessmentResult::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->orderBy('cobit_id')
            ->orderBy('level')
            ->get()
            ->groupBy('cobit_id');

        $respondents = AssessmentRespondent::with('user')
            ->where('assessment_id', $assessment->id)
            ->get();

        return view('assessor.capability.result', compact(
            'assessment',
            'objectives',
            'results',
            'respondents'
        ));
    }

    public function recalculate(Assessment $assessment)
    {
        abort_if($assessment->created_by !== Auth::id(), 403);

        (new CapabilityCalculator())->calculate($assessment);

        return redirect()
            ->route('assessor.capability.result', $assessment)
            ->with('success', 'Hasil capability berhasil dihitung ulang.');
    }

    public function judge(Assessment $assessment)
    {
        abort_if($assessment->created_by !== Auth::id(), 403);

        $total     = $assessment->respondents()->count();
        $completed = $assessment->respondents()->where('status', 'completed')->count();

        if ($total === 0 || $completed < $total) {
            return redirect()
                ->route('assessor.capability.index')
                ->with('error', 'Semua responden harus selesai mengisi terlebih dahulu.');
        }

        $assessment->load('department');

        $objectives = AssessmentObjective::with(['cobit.statements' => fn ($q) => $q->orderBy('level')])
            ->where('assessment_id', $assessment->id)
            ->orderByDesc('score')
            ->get();

        $results = AssessmentResult::where('assessment_id', $assessment->id)
            ->get()
            ->groupBy('cobit_id');

        // Ambil judgment yang sudah pernah disimpan
        $judgments = AssessmentJudgment::where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('cobit_id');

            $objectives->each(function ($obj) use ($results, $judgments) {
            $cobitResults = ($results->get($obj->cobit_id) ?? collect())->keyBy('level');
            $firstUnfulfilledLevel = null;

            for ($lv = 0; $lv <= 5; $lv++) {
                $res = $cobitResults->get($lv);
                $percentage = $res
                    ? (float) $res->percentage
                    : 0;
                if ($percentage < 50) {
                    $firstUnfulfilledLevel = $lv;
                    break;
                }
            }

            if ($firstUnfulfilledLevel === null) {
                $recommendedLevel = 5;
            } else {
                $recommendedLevel = max(
                    0,
                    $firstUnfulfilledLevel - 1
                );
            }

            $obj->recommended_level = $recommendedLevel;
            $obj->first_unfulfilled_level = $firstUnfulfilledLevel;
            $existing = $judgments->get($obj->cobit_id);

            if ($existing && !empty($existing->activities)) {
                $obj->assessment_activities = $existing->activities;
            } else {
                if ($firstUnfulfilledLevel !== null) {
                    $obj->assessment_activities =
                        $obj->cobit
                            ->statements
                            ->where('level', $firstUnfulfilledLevel)
                            ->pluck('statement')
                            ->values()
                            ->toArray();
                } else {$obj->assessment_activities = [];}
            }
        });

        return view('assessor.capability.judge', compact(
            'assessment',
            'objectives',
            'results',
            'judgments'
        ));
    }

    /**
     * Simpan penilaian assessor
     */
    public function storeJudge(Request $request, Assessment $assessment)
    {
        abort_if($assessment->created_by !== Auth::id(), 403);

        $request->validate([
            'judgments'                     => 'required|array',
            'judgments.*.cobit_id'          => 'required|integer',
            'judgments.*.recommended_level' => 'nullable|integer|min:0|max:5',
            'judgments.*.achieved_level'    => 'required|integer|min:0|max:5',
            'judgments.*.activities'        => 'nullable|array',
            'judgments.*.activities.*'      => 'nullable|string|max:2000',
            'judgments.*.notes'             => 'nullable|string|max:2000',
            'judgments.*.evidence'          => 'nullable|array',
            'judgments.*.evidence.*'        => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:10240', // 10MB
        ]);

        // foreach ($request->judgments as $item) {
        //     $data = [
        //         'achieved_level' => $item['achieved_level'],
        //         'notes'          => $item['notes'] ?? null,
        //     ];

        //     // Handle upload evidence
        //     if (isset($item['evidence']) && $item['evidence'] instanceof \Illuminate\Http\UploadedFile) {
        //         $path = $item['evidence']->store(
        //             "capability-evidence/{$assessment->id}",
        //             'public'
        //         );
        //         $data['evidence_path'] = $path;
        //     }

        //     AssessmentJudgment::updateOrCreate(
        //         [
        //             'assessment_id' => $assessment->id,
        //             'cobit_id'      => $item['cobit_id'],
        //         ],
        //         $data
        //     );
        // }
        DB::transaction(function () use ($request, $assessment) {
            foreach ($request->input('judgments', []) as $index => $item) {
                $cobitId = $item['cobit_id'];
                $judgment =AssessmentJudgment::where('assessment_id', $assessment->id)
                    ->where('cobit_id', $cobitId)
                    ->first();

                $activities = collect($item['activities'] ?? [])
                    ->map(fn ($activity) => trim($activity))
                    ->filter(fn ($activity) => $activity !== '')
                    ->values()
                    ->toArray();

                $data = [
                    'recommended_level' => $item['recommended_level'] ?? null,
                    'activities' => $activities,
                    'achieved_level' => $item['achieved_level'],
                    'notes' => $item['notes'] ?? null,
                ];

                $newEvidencePaths = [];

                if ($request->hasFile("judgments.$index.evidence")) {
                    foreach (
                        $request->file("judgments.$index.evidence") as $file
                    ) {
                        if (!$file->isValid()) {
                            continue;
                        }
                        $path = $file->store(
                            "capability-evidence/{$assessment->id}/{$cobitId}",
                            'public'
                        );
                        $newEvidencePaths[] = $path;
                    }
                }

                $oldEvidencePaths = [];

                if ($judgment) {
                    $oldEvidencePaths = $judgment->evidence_paths ?? [];
                    if (empty($oldEvidencePaths) && !empty($judgment->evidence_path)
                    ) {
                        $oldEvidencePaths = [$judgment->evidence_path];
                    }
                }

                $allEvidencePaths = array_values(array_unique(array_merge($oldEvidencePaths, $newEvidencePaths)));

                $data['evidence_paths'] = $allEvidencePaths;

                $data['evidence_path'] = $allEvidencePaths[0] ?? null;

                AssessmentJudgment::updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'cobit_id' => $cobitId,
                    ],
                    $data
                );
            }
        });

        return redirect()
            ->route('assessor.capability.judge', $assessment)
            ->with('success', 'Penilaian assessor berhasil disimpan.');
    }

    public function gapIndex()
    {
        // $assessments = Assessment::with(['department'])
        //     ->withCount(['assessmentObjectives as objective_priorities_count'])
        //     ->withCount(['respondents as respondents_count'])
        //     ->withCount(['respondents as completed_respondents_count' => fn ($q) => $q->where('status', 'completed')])
        //     ->where('created_by', Auth::id())
        //     ->whereHas('assessmentObjectives')
        //     ->latest()
        //     ->get()
        //     ->map(function ($a) {
        //         $a->is_all_departments = (bool) ($a->is_all_department ?? false);
        $assessments = Assessment::with(['department'])
            ->withCount(['assessmentObjectives as objective_priorities_count'])
            ->withCount(['respondents as respondents_count'])
            ->withCount(['respondents as completed_respondents_count' => fn ($q) => $q->where('status', 'completed')])
            ->where('created_by', Auth::id())
            ->whereHas('assessmentObjectives')
            ->latest()
            ->paginate(10);

        $assessments->getCollection()->transform(function ($a) {
            $a->is_all_departments = (bool) ($a->is_all_department ?? false);

                // Hitung ringkasan gap dari judgment
                $judgments = \App\Models\AssessmentJudgment::where('assessment_id', $a->id)->get();

                $totalJudged = $judgments->count();
                $gapClosed   = $judgments->filter(fn ($j) => 
                    $j->recommended_level !== null && 
                    $j->achieved_level !== null && 
                    $j->achieved_level == $j->recommended_level
                )->count();

                $gapOpen = $judgments->filter(fn ($j) => 
                    $j->recommended_level !== null && 
                    $j->achieved_level !== null && 
                    $j->achieved_level != $j->recommended_level
                )->count();

                $a->total_judged   = $totalJudged;
                $a->gap_closed     = $gapClosed;
                $a->gap_open       = $gapOpen;
                $a->has_judgment   = $totalJudged > 0;

                return $a;
            });

        return view('assessor.gap-analysis.index', compact('assessments'));
    }

    public function gap(Assessment $assessment)
    {
        abort_if($assessment->created_by !== Auth::id(), 403);

        $assessment->load('department');

        $judgments = AssessmentJudgment::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('cobit_id');

        $objectives = AssessmentObjective::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->orderByDesc('score')
            ->get();

        $domains = $objectives->map(function ($obj) use ($judgments) {
            $judgment = $judgments->get($obj->cobit_id);

            $recommended = $judgment?->recommended_level !== null
                ? (int) $judgment->recommended_level
                : null;

            $achieved = $judgment?->achieved_level !== null
                ? (int) $judgment->achieved_level
                : null;

            $gap = ($recommended !== null && $achieved !== null)
                ? $achieved - $recommended
                : null;

            return (object) [
                'id'                => $obj->id,
                'cobit_id'          => $obj->cobit_id,
                'code'              => $obj->cobit->code ?? $obj->cobit->process_id ?? '-',
                'name'              => $obj->cobit->name ?? $obj->cobit->process_name ?? '-',
                'description'       => $obj->cobit->description ?? null,
                'recommended_level' => $recommended,
                'assessor_level'    => $achieved,
                'achieved_level'    => $achieved,
                'gap'               => $gap,
                'notes'             => $judgment?->notes,
                'has_judgment'      => $judgment !== null,
            ];
        });

        return view('assessor.gap-analysis.show', compact('assessment', 'domains'));
    }

    /**
     * Daftar assessment yang siap untuk prioritas perbaikan
     */
    public function priorityIndex()
    {
        $assessments = Assessment::with(['department'])
            ->withCount(['assessmentObjectives as objective_priorities_count'])
            ->where('created_by', Auth::id())
            ->whereHas('assessmentObjectives')
            ->latest()
            ->paginate(10);

        $assessments->getCollection()->transform(function ($a) {
            $a->is_all_departments = (bool) ($a->is_all_department ?? false);

                $judgments = AssessmentJudgment::where('assessment_id', $a->id)->get();
                $a->has_judgment = $judgments->count() > 0;
                $a->total_judged = $judgments->count();

                // Hitung jumlah yang masih gap (perlu perbaikan)
                $a->need_improvement = $judgments->filter(function ($j) {
                    return $j->recommended_level !== null
                        && $j->achieved_level !== null
                        && $j->achieved_level < $j->recommended_level;
                })->count();

                return $a;
            });

        return view('assessor.priority.index', compact('assessments'));
    }

    /**
     * Halaman detail Prioritas Perbaikan (sesuai gambar)
     */
    public function priority(Assessment $assessment)
    {
        abort_if($assessment->created_by !== Auth::id(), 403);

        $assessment->load('department');

        $judgments = AssessmentJudgment::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('cobit_id');

        $objectives = AssessmentObjective::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->orderByDesc('score')
            ->get();

        $items = $objectives->map(function ($obj) use ($judgments) {
            $judgment = $judgments->get($obj->cobit_id);

            $recommended = $judgment?->recommended_level !== null ? (int) $judgment->recommended_level : null;
            $achieved    = $judgment?->achieved_level !== null ? (int) $judgment->achieved_level : null;

            // Gap = selisih yang masih kurang (positif = butuh perbaikan)
            // Jika belum ada judgment atau sudah tercapai → gap = 0
            $gap = ($recommended !== null && $achieved !== null)
                ? max(0, $recommended - $achieved)
                : 0;

            // Risk & Impact (skala 1-5)
            $score  = (float) ($obj->score ?? 0);
            $impact = min(5, max(3, (int) ceil($score / 20) + 2)); // 3-5
            $risk   = min(5, max(3, $gap > 0 ? (3 + $gap) : 3));   // 3-5

            // Priority score. Gap 0 → score 0 → otomatis ke belakang
            $priorityScore = $gap * $risk * $impact;

            // Ambil nama domain dengan fallback yang lebih lengkap
            $cobit = $obj->cobit;
            $code = $cobit->code
                ?? $cobit->process_id
                ?? $cobit->process_code
                ?? '-';

            $name = $cobit->process_name
                ?? $cobit->name
                ?? $cobit->title
                ?? $cobit->process_title
                ?? $cobit->description
                ?? null;

            // Kalau masih kosong, coba dari relasi lain atau biarkan null
            if (empty($name) && method_exists($cobit, 'getNameAttribute')) {
                $name = $cobit->name;
            }

            return (object) [
                'cobit_id'       => $obj->cobit_id,
                'code'           => $code,
                'name'           => $name ?: '—',
                'gap'            => $gap,
                'risk'           => $risk,
                'impact'         => $impact,
                'priority_score' => $priorityScore,
                'has_judgment'   => $judgment !== null,
                'recommended'    => $recommended,
                'achieved'       => $achieved,
            ];
        })
        // TIDAK difilter → semua domain ikut
        ->sortByDesc(function ($item) {
            // Urutkan: score tertinggi dulu, kalau sama urutkan gap tertinggi
            return [$item->priority_score, $item->gap];
        })
        ->values();

        // Tentukan label Priority & Status (Prioritas 1, 2, 3...)
        $items = $items->map(function ($item, $index) {
            $rank = $index + 1;

            if ($item->priority_score >= 60) {
                $item->priority_label = 'VERY HIGH';
            } elseif ($item->priority_score >= 35) {
                $item->priority_label = 'HIGH';
            } elseif ($item->priority_score >= 15) {
                $item->priority_label = 'MEDIUM';
            } elseif ($item->priority_score > 0) {
                $item->priority_label = 'LOW';
            } else {
                // Gap 0 → tidak perlu perbaikan
                $item->priority_label = 'NONE';
            }

            $item->rank = $rank;
            $item->status_label = 'Prioritas ' . $rank;

            // Warna dot
            if ($item->priority_score <= 0) {
                $item->dot_color = '#94a3b8'; // abu-abu (sudah OK)
            } elseif ($rank === 1) {
                $item->dot_color = '#ef4444'; // merah
            } elseif ($rank <= 4) {
                $item->dot_color = '#f97316'; // orange
            } else {
                $item->dot_color = '#eab308'; // kuning
            }

            return $item;
        });

        return view('assessor.priority.show', compact('assessment', 'items'));
    }

    /**
     * Daftar assessment yang siap untuk roadmap
     */
    public function roadmapIndex()
    {
        $assessments = Assessment::with(['department'])
            ->where('created_by', Auth::id())
            ->whereHas('assessmentObjectives')
            ->latest()
            ->paginate(10);

        $assessments->getCollection()->transform(function ($a) {
            $a->is_all_departments = (bool) ($a->is_all_department ?? false);

                $judgments = AssessmentJudgment::where('assessment_id', $a->id)->get();
                $a->has_judgment = $judgments->count() > 0;
                $a->total_judged = $judgments->count();

                $a->need_improvement = $judgments->filter(function ($j) {
                    return $j->recommended_level !== null
                        && $j->achieved_level !== null
                        && $j->achieved_level < $j->recommended_level;
                })->count();

                return $a;
            });

        return view('assessor.roadmap.index', compact('assessments'));
    }

    /**
     * Halaman detail Roadmap Transformasi Digital (sesuai gambar)
     */
    public function roadmap(Assessment $assessment)
    {
        abort_if($assessment->created_by !== Auth::id(), 403);

        $assessment->load('department');

        $judgments = AssessmentJudgment::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('cobit_id');

        $objectives = AssessmentObjective::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->orderByDesc('score')
            ->get();

        $items = $objectives->map(function ($obj) use ($judgments) {
            $judgment = $judgments->get($obj->cobit_id);

            $recommended = $judgment?->recommended_level !== null ? (int) $judgment->recommended_level : null;
            $achieved    = $judgment?->achieved_level !== null ? (int) $judgment->achieved_level : null;

            // Gap = selisih yang masih kurang (positif = butuh perbaikan)
            $gap = ($recommended !== null && $achieved !== null)
                ? max(0, $recommended - $achieved)
                : 0;

            $score  = (float) ($obj->score ?? 0);
            $impact = min(5, max(3, (int) ceil($score / 20) + 2));
            $risk   = min(5, max(3, $gap > 0 ? (3 + $gap) : 3));

            $priorityScore = $gap * $risk * $impact;

            // Ambil code & name dengan fallback yang robust
            $cobit = $obj->cobit;

            $code = $cobit->code
                ?? $cobit->process_id
                ?? $cobit->process_code
                ?? '-';

            $name = $cobit->process_name
                ?? $cobit->name
                ?? $cobit->title
                ?? $cobit->process_title
                ?? $cobit->description
                ?? null;

            if (empty($name) && method_exists($cobit, 'getNameAttribute')) {
                $name = $cobit->name;
            }

            return (object) [
                'code'           => $code,
                'name'           => $name ?: '—',
                'gap'            => $gap,
                'priority_score' => $priorityScore,
                'has_gap'        => $gap > 0,
            ];
        })
        // Semua domain ikut, urutkan dari prioritas tertinggi
        ->sortByDesc(function ($item) {
            return [$item->priority_score, $item->gap];
        })
        ->values();

        $total = $items->count();

        if ($total === 0) {
            $shortTerm = $mediumTerm = $longTerm = collect();
        } else {
            // Bagi menjadi 3 fase secara merata
            // Domain dengan gap tinggi otomatis masuk fase awal karena sudah di-sort
            $shortCount  = max(1, (int) ceil($total / 3));
            $mediumCount = max(1, (int) ceil(($total - $shortCount) / 2));

            $shortTerm  = $items->take($shortCount)->values();
            $mediumTerm = $items->slice($shortCount, $mediumCount)->values();
            $longTerm   = $items->slice($shortCount + $mediumCount)->values();
        }

        if ($assessment->status !== 'closed') {
            $assessment->update(['status' => 'closed']);
        }
        
        return view('assessor.roadmap.show', compact(
            'assessment',
            'shortTerm',
            'mediumTerm',
            'longTerm'
        ));
    }
}