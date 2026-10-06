<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentObjective;
use App\Services\CobitObjectiveGenerator;
use Illuminate\Support\Facades\Auth;

class ObjectiveController extends Controller
{
    public function index()
    {
        $assessments = Assessment::with('department')
            ->where('created_by', Auth::id())
            ->whereHas('designFactorAnswers')
            ->withCount('designFactorAnswers')
            ->withCount('assessmentObjectives as objective_priorities_count') // penting!
            ->latest()
            ->paginate(10);

        // Samakan atribut is_all_departments jika view membutuhkannya
        $assessments->transform(function ($a) {
            $a->is_all_departments = (bool) ($a->is_all_department ?? false);
            return $a;
        });

        return view('assessor.objectives.index', compact('assessments'));
    }

    public function generateObjectives(Assessment $assessment)
    {
        abort_if($assessment->created_by !== Auth::id(), 403);

        if (!$assessment->designFactorAnswers()->exists()) {
            return redirect()
                ->route('assessor.design-factors.show', $assessment)
                ->with('error', 'Isi Design Factor terlebih dahulu.');
        }

        $generator = new CobitObjectiveGenerator();
        $results   = $generator->generate($assessment);

        if ($results->isEmpty()) {
            return redirect()
                ->route('assessor.design-factors.result', $assessment)
                ->with('error', 'Tidak ada Governance & Management Objective yang dapat direkomendasikan dari hasil Design Factor. Periksa mapping value option Design Factor.');
        }

        return redirect()
            ->route('assessor.objectives.show', $assessment)
            ->with('success', 'Governance & Management Objectives berhasil digenerate (' . $results->count() . ' objectives).');
    }

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
                ->route('assessor.objectives.index')
                ->with('error', 'Belum ada Governance & Management Objective yang digenerate.');
        }

        $grouped = [
            'high'   => $objectives->where('priority', 'high'),
            'medium' => $objectives->where('priority', 'medium'),
            'low'    => $objectives->where('priority', 'low'),
        ];

        $stats = [
            'high'   => $grouped['high']->count(),
            'medium' => $grouped['medium']->count(),
            'low'    => $grouped['low']->count(),
            'total'  => $objectives->count(),
        ];

        return view('assessor.objectives.show', compact(
            'assessment',
            'objectives',
            'grouped',
            'stats'
        ));
    }
}