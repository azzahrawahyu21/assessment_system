<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentRespondent;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentObjective;
use App\Models\CobitStatement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $respondents = AssessmentRespondent::with(['assessment.objectives.cobit'])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->get();

        return response()->json($respondents);
    }

    public function show($respondentId)
    {
        $respondent = AssessmentRespondent::with(['assessment'])
            ->where('user_id', Auth::id())
            ->findOrFail($respondentId);

        $objectives = AssessmentObjective::where('assessment_id', $respondent->assessment_id)
            ->with(['cobit.statements' => fn($q) => $q->orderBy('level')])
            ->get();

        $existingAnswers = AssessmentAnswer::where('respondent_id', $respondent->id)
            ->get()
            ->keyBy('cobit_statement_id');

        return response()->json([
            'respondent' => $respondent,
            'objectives' => $objectives,
            'existing_answers' => $existingAnswers,
        ]);
    }

    public function store(Request $request, $respondentId)
    {
        $respondent = AssessmentRespondent::where('user_id', Auth::id())
            ->findOrFail($respondentId);

        if ($respondent->status === 'completed') {
            return response()->json(['message' => 'Kuesioner sudah diselesaikan'], 422);
        }

        $request->validate([
            'answers' => 'required|array',
            'answers.*.cobit_statement_id' => 'required|exists:cobit_statements,id_statement',
            'answers.*.answer' => 'required|boolean',
        ]);

        DB::transaction(function () use ($request, $respondent) {
            foreach ($request->answers as $answer) {
                AssessmentAnswer::updateOrCreate(
                    [
                        'respondent_id' => $respondent->id,
                        'cobit_statement_id' => $answer['cobit_statement_id'],
                    ],
                    [
                        'assessment_id' => $respondent->assessment_id,
                        'answer' => $answer['answer'],
                    ]
                );
            }

            $respondent->update(['status' => 'in_progress']);
        });

        return response()->json([
            'message' => 'Jawaban berhasil disimpan',
            'data' => $respondent->fresh(),
        ]);
    }

    public function complete($respondentId)
    {
        $respondent = AssessmentRespondent::where('user_id', Auth::id())
            ->findOrFail($respondentId);

        $objectiveIds = AssessmentObjective::where('assessment_id', $respondent->assessment_id)
            ->pluck('cobit_id');

        $totalStatements = CobitStatement::whereIn('cobit_id', $objectiveIds)->count();
        $answered = AssessmentAnswer::where('respondent_id', $respondent->id)->count();

        if ($answered < $totalStatements) {
            return response()->json([
                'message' => 'Masih ada pernyataan yang belum dijawab',
                'answered' => $answered,
                'total' => $totalStatements,
            ], 422);
        }

        $respondent->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Kuesioner berhasil diselesaikan. Terima kasih!',
            'data' => $respondent,
        ]);
    }

    public function history()
    {
        $respondents = AssessmentRespondent::with(['assessment'])
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->latest()
            ->paginate(10);

        return response()->json($respondents);
    }
}