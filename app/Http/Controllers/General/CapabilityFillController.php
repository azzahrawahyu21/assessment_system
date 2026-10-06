<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentObjective;
use App\Models\AssessmentRespondent;
use App\Models\Cobit;
use App\Models\CobitStatement;
use App\Services\CapabilityCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CapabilityFillController extends Controller
{
    /**
     * Daftar assessment yang ditugaskan ke user login (responden).
     */
    public function index()
    {
        $respondents = AssessmentRespondent::with(['assessment.department'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $assessments = $respondents
            ->map(fn ($r) => $r->assessment)
            ->filter()
            ->values();

        return view('assessor.capability.index', [
            'assessments'    => $assessments,
            'asRespondent'   => true,
            'respondentsMap' => $respondents->keyBy('assessment_id'),
        ]);
    }

    /**
     * Daftar domain (objectives) untuk diisi responden.
     */
    public function show(Assessment $assessment)
    {
        $respondent = AssessmentRespondent::where('assessment_id', $assessment->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $assessment->load('department');

        $objectives = AssessmentObjective::with('cobit')
            ->where('assessment_id', $assessment->id)
            ->orderByDesc('score')
            ->get();

        return view('assessor.capability.show', [
            'assessment'   => $assessment,
            'objectives'   => $objectives,
            'respondents'  => collect([$respondent]),
            'asRespondent' => true,
            'respondent'   => $respondent,
        ]);
    }

    /**
     * Form Ya/Tidak — pernyataan per level untuk satu domain (cobit).
     */
    public function fill(Assessment $assessment, $cobit)
    {
        $respondent = AssessmentRespondent::where('assessment_id', $assessment->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cobit = Cobit::where('id_cobit', $cobit)->firstOrFail();

        $objective = AssessmentObjective::where('assessment_id', $assessment->id)
            ->where('cobit_id', $cobit->id_cobit)
            ->firstOrFail();

        $statements = CobitStatement::where('cobit_id', $cobit->id_cobit)
            ->orderBy('level')
            ->orderBy('id_statement')
            ->get()
            ->groupBy('level');

        $statementIds = $statements->flatten()->pluck('id_statement');

        $answers = AssessmentAnswer::where('respondent_id', $respondent->id)
            ->whereIn('cobit_statement_id', $statementIds)
            ->get()
            ->keyBy('cobit_statement_id');

        return view('assessor.capability.fill-form', [
            'assessment' => $assessment,
            'cobit'      => $cobit,
            'objective'  => $objective,
            'statements' => $statements,
            'answers'    => $answers,
            'respondent' => $respondent,
        ]);
    }

    /**
     * Simpan jawaban Ya/Tidak (draft atau progres domain).
     */
    public function store(Request $request, Assessment $assessment, $cobit)
    {
        $respondent = AssessmentRespondent::where('assessment_id', $assessment->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($respondent->status === 'completed') {
            return redirect()
                ->route('capability.fill.show', $assessment)
                ->with('error', 'Penilaian sudah dikirim dan tidak dapat diubah.');
        }

        $cobit = Cobit::where('id_cobit', $cobit)->firstOrFail();

        // Pastikan cobit termasuk objective assessment ini
        AssessmentObjective::where('assessment_id', $assessment->id)
            ->where('cobit_id', $cobit->id_cobit)
            ->firstOrFail();

        $request->validate([
            'answers'   => ['required', 'array'],
            'answers.*' => ['nullable', 'in:0,1'],
            'submit'    => ['nullable', 'in:0,1'],
        ]);

        $validIds = CobitStatement::where('cobit_id', $cobit->id_cobit)
            ->pluck('id_statement')
            ->all();

        $isFinal = (bool) $request->input('submit', false);

        DB::transaction(function () use ($request, $assessment, $respondent, $validIds, $isFinal) {
            foreach ($request->input('answers', []) as $statementId => $value) {
                if (! in_array((int) $statementId, $validIds, true)) {
                    continue;
                }

                if ($value === null || $value === '') {
                    continue;
                }

                AssessmentAnswer::updateOrCreate(
                    [
                        'respondent_id'      => $respondent->id,
                        'cobit_statement_id' => (int) $statementId,
                    ],
                    [
                        'assessment_id' => $assessment->id,
                        'answer'        => (bool) $value,
                    ]
                );
            }

            // Tandai completed hanya jika SEMUA statement SEMUA objective sudah dijawab
            if ($isFinal) {
                $allStatementIds = CobitStatement::whereIn(
                    'cobit_id',
                    AssessmentObjective::where('assessment_id', $assessment->id)->pluck('cobit_id')
                )->pluck('id_statement');

                $answeredCount = AssessmentAnswer::where('respondent_id', $respondent->id)
                    ->whereIn('cobit_statement_id', $allStatementIds)
                    ->count();

                if ($allStatementIds->count() > 0 && $answeredCount >= $allStatementIds->count()) {
                    $respondent->update([
                        'status'       => 'completed',
                        'completed_at' => now(),
                    ]);

                    (new CapabilityCalculator())->calculate($assessment);
                } else {
                    $respondent->update(['status' => 'in_progress']);
                }
            } else {
                $respondent->update(['status' => 'in_progress']);
            }
        });

        $message = $isFinal
            ? 'Jawaban domain disimpan. Lengkapi semua domain; status selesai otomatis jika semua pernyataan sudah dijawab.'
            : 'Draft jawaban domain ini disimpan.';

        return redirect()
            ->route('capability.fill.show', $assessment)
            ->with('success', $message);
    }

    public function complete(Assessment $assessment)
    {
        $respondent = AssessmentRespondent::where('assessment_id', $assessment->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($respondent->status === 'completed') {
            return redirect()
                ->route('capability.fill.show', $assessment)
                ->with('error', 'Jawaban sudah pernah dikirim dan tidak dapat diubah.');
        }

        // Cek apakah semua statement dari semua objective sudah dijawab
        $allStatementIds = CobitStatement::whereIn(
            'cobit_id',
            AssessmentObjective::where('assessment_id', $assessment->id)->pluck('cobit_id')
        )->pluck('id_statement');

        $answeredCount = AssessmentAnswer::where('respondent_id', $respondent->id)
            ->whereIn('cobit_statement_id', $allStatementIds)
            ->count();

        if ($allStatementIds->isEmpty()) {
            return redirect()
                ->route('capability.fill.show', $assessment)
                ->with('error', 'Tidak ada pernyataan yang harus dijawab.');
        }

        if ($answeredCount < $allStatementIds->count()) {
            $remaining = $allStatementIds->count() - $answeredCount;
            return redirect()
                ->route('capability.fill.show', $assessment)
                ->with('error', "Masih ada {$remaining} pernyataan yang belum dijawab. Lengkapi semua domain terlebih dahulu.");
        }

        DB::transaction(function () use ($respondent, $assessment) {
            $respondent->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);

            // Hitung ulang hasil capability
            (new CapabilityCalculator())->calculate($assessment);
        });

        return redirect()
            ->route('capability.fill.index')
            ->with('success', 'Semua jawaban berhasil dikirim. Terima kasih!');
    }
}