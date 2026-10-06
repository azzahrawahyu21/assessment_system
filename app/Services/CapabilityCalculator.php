<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentObjective;
use App\Models\AssessmentRespondent;
use App\Models\AssessmentResult;
use App\Models\CobitStatement;
use Illuminate\Support\Facades\DB;

class CapabilityCalculator
{
    /**
     * Hitung & simpan hasil capability untuk 1 assessment.
     */
    public function calculate(Assessment $assessment): void
    {
        $completedRespondents = AssessmentRespondent::where(
                'assessment_id',
                $assessment->id
            )
            ->where('status', 'completed')
            ->get();

        if ($completedRespondents->isEmpty()) {
            return;
        }

        /*
         * Ambil semua COBIT objective yang
         * direkomendasikan untuk assessment ini.
         */
        $objectives = AssessmentObjective::where(
                'assessment_id',
                $assessment->id
            )
            ->get();

        $cobitIds = $objectives
            ->pluck('cobit_id')
            ->unique()
            ->filter()
            ->values();

        if ($cobitIds->isEmpty()) {
            return;
        }

        /*
         * Ambil semua statement.
         *
         * Tidak menggunakan sort_order karena
         * kolom tersebut tidak ada pada tabel
         * cobit_statements.
         */
        $statements = CobitStatement::whereIn(
                'cobit_id',
                $cobitIds
            )
            ->orderBy('level')
            ->orderBy('id_statement')
            ->get()
            ->groupBy(
                fn ($statement) =>
                    $statement->cobit_id
                    . '_'
                    . $statement->level
            );

        DB::transaction(function () use (
            $assessment,
            $completedRespondents,
            $statements,
            $cobitIds
        ) {

            /*
             * Hapus hasil lama agar hasil selalu
             * merupakan hasil perhitungan terbaru.
             */
            AssessmentResult::where(
                'assessment_id',
                $assessment->id
            )->delete();

            foreach ($cobitIds as $cobitId) {

                for ($level = 0; $level <= 5; $level++) {

                    $key = $cobitId . '_' . $level;

                    $levelStatements =
                        $statements->get(
                            $key,
                            collect()
                        );

                    if ($levelStatements->isEmpty()) {
                        continue;
                    }

                    $statementIds =
                        $levelStatements
                            ->pluck('id_statement');

                    $totalStmt =
                        $statementIds->count();

                    if ($totalStmt === 0) {
                        continue;
                    }

                    $percentages = [];

                    foreach (
                        $completedRespondents
                        as $respondent
                    ) {

                        $yesCount =
                            AssessmentAnswer::where(
                                'respondent_id',
                                $respondent->id
                            )
                            ->whereIn(
                                'cobit_statement_id',
                                $statementIds
                            )
                            ->where(
                                'answer',
                                true
                            )
                            ->count();

                        $percentage =
                            ($yesCount / $totalStmt) * 100;

                        $percentages[] =
                            $percentage;
                    }

                    $avgPercentage =
                        count($percentages) > 0
                            ? round(
                                array_sum($percentages)
                                / count($percentages),
                                2
                            )
                            : 0;

                    AssessmentResult::create([
                        'assessment_id' => $assessment->id,
                        'cobit_id'      => $cobitId,
                        'level'         => $level,
                        'percentage'    => $avgPercentage,
                        'rating'        =>
                            AssessmentResult::calculateRating(
                                $avgPercentage
                            ),
                    ]);
                }
            }
        });
    }
}