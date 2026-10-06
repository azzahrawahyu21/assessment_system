<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentDesignFactorAnswer;
use App\Models\AssessmentObjective;
use App\Models\DesignFactor;
use App\Models\DesignFactorOption;
use App\Models\Cobit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CobitObjectiveGenerator
{
    protected array $mapping = [
        'DF01' => [
            'growth'     => ['EDM02', 'APO05', 'BAI01', 'BAI03'],
            'client'     => ['APO08', 'APO09', 'DSS01', 'DSS02'],
            'cost'       => ['EDM02', 'APO06', 'APO09', 'BAI04'],
            'innovation' => ['EDM02', 'APO04', 'APO05', 'BAI03'],
        ],
        'DF02' => [
            'EG01' => ['EDM02', 'APO05', 'APO09', 'BAI01', 'BAI03'],
            'EG02' => ['EDM03', 'APO12', 'APO13', 'MEA03'],
            'EG03' => ['EDM01', 'APO01', 'APO12', 'APO13', 'MEA03'],
            'EG04' => ['EDM02', 'APO06', 'APO11', 'MEA01'],
            'EG05' => ['APO08', 'APO09', 'DSS01', 'DSS02'],
            'EG06' => ['EDM03', 'APO12', 'APO13', 'DSS04'],
            'EG07' => ['APO01', 'APO09', 'APO11', 'MEA01'],
            'EG08' => ['APO09', 'APO11', 'BAI03', 'BAI04'],
            'EG09' => ['EDM02', 'APO06', 'APO09', 'BAI04'],
            'EG10' => ['APO07', 'APO08', 'BAI05'],
            'EG11' => ['EDM01', 'APO01', 'APO12', 'MEA03'],
            'EG12' => ['EDM02', 'APO02', 'APO04', 'BAI01', 'BAI03'],
            'EG13' => ['EDM02', 'APO04', 'APO05', 'BAI03'],
        ],
        'DF03' => [
            'IT_investment'        => ['EDM02', 'EDM03', 'APO05', 'APO06'],
            'program_lifecycle'    => ['APO05', 'BAI01', 'BAI03', 'BAI05', 'BAI06'],
            'IT_expertise'         => ['APO07', 'APO08', 'BAI05'],
            'staff_operations'     => ['APO07', 'DSS01', 'DSS03'],
            'information'          => ['APO14', 'APO13', 'DSS05', 'MEA01'],
            'architecture'         => ['APO03', 'BAI03', 'BAI04'],
            'infrastructure'       => ['APO03', 'BAI04', 'DSS01', 'DSS04'],
            'software'             => ['APO03', 'BAI03', 'BAI04', 'DSS01'],
            'business_continuity'  => ['EDM03', 'APO12', 'APO13', 'DSS04'],
            'unauthorized_actions' => ['APO13', 'DSS05', 'MEA03'],
            'software_adoption'    => ['APO11', 'BAI03', 'BAI05', 'DSS01'],
            'hardware_incidents'   => ['APO12', 'BAI04', 'DSS01', 'DSS04'],
            'software_failures'    => ['BAI03', 'BAI06', 'DSS01', 'DSS03'],
            'logical_attacks'      => ['APO13', 'DSS05', 'MEA03'],
            'third_party'          => ['APO10', 'APO12', 'DSS01', 'MEA03'],
            'noncompliance'        => ['EDM01', 'APO01', 'APO12', 'MEA03'],
            'geopolitical'         => ['EDM03', 'APO12', 'APO13'],
            'industrial_action'    => ['APO07', 'APO12', 'DSS01'],
            'acts_of_nature'       => ['APO12', 'DSS04'],
            'innovation'           => ['APO04', 'APO05', 'BAI03'],
        ],
        'DF04' => [
            'frustration'            => ['APO01', 'APO07', 'APO08'],
            'low_quality'            => ['APO11', 'APO09', 'DSS01', 'MEA01'],
            'significant_incidents'  => ['APO12', 'DSS02', 'DSS03', 'DSS04'],
            'service_delivery'       => ['APO10', 'APO09', 'DSS01', 'DSS02'],
            'failures'               => ['EDM01', 'APO01', 'APO12', 'MEA03'],
            'regular_audit'          => ['EDM01', 'APO01', 'MEA02', 'MEA03'],
            'business_dissatisfied'  => ['APO08', 'APO09', 'APO11', 'MEA01'],
            'board_concerns'         => ['EDM01', 'EDM02', 'EDM03', 'APO01'],
            'complex_architecture'   => ['APO03', 'BAI03', 'BAI04'],
            'high_spending'          => ['EDM02', 'APO06', 'APO09'],
            'dissatisfied_users'     => ['APO08', 'APO09', 'DSS01', 'DSS02'],
            'multiple_initiatives'   => ['APO05', 'BAI01', 'BAI03', 'BAI05'],
            'data_quality'           => ['APO14', 'APO11', 'DSS06', 'MEA01'],
            'lack_knowledge'         => ['APO07', 'BAI05', 'BAI08'],
            'inability_exploit'      => ['APO04', 'APO05', 'BAI03'],
        ],
        'DF05' => [
            'normal' => ['APO12', 'APO13', 'DSS05'],
            'high'   => ['EDM03', 'APO12', 'APO13', 'DSS05', 'MEA03'],
        ],
        'DF06' => [
            'low'    => ['APO01', 'MEA03'],
            'normal' => ['EDM01', 'APO01', 'MEA03'],
            'high'   => ['EDM01', 'APO01', 'APO12', 'MEA03', 'MEA04'],
        ],
        'DF07' => [
            'support'    => ['APO01', 'APO07', 'DSS01', 'DSS02'],
            'factory'    => ['APO09', 'APO10', 'DSS01', 'DSS03'],
            'turnaround' => ['APO02', 'APO04', 'BAI01', 'BAI03'],
            'strategic'  => ['EDM01', 'EDM02', 'APO02', 'APO05', 'BAI01'],
        ],
        'DF08' => [
            'outsourcing' => ['APO09', 'APO10', 'APO12', 'MEA03'],
            'cloud'       => ['APO09', 'APO10', 'APO12', 'DSS01', 'DSS05'],
            'insourced'   => ['APO01', 'APO07', 'APO09', 'DSS01'],
            'hybrid'      => ['APO09', 'APO10', 'APO12', 'DSS01'],
        ],
        'DF09' => [
            'agile'       => ['APO04', 'APO05', 'BAI02', 'BAI03', 'BAI05'],
            'devops'      => ['APO04', 'BAI03', 'BAI06', 'BAI07', 'DSS01'],
            'traditional' => ['BAI01', 'BAI02', 'BAI03', 'BAI05', 'BAI07'],
            'hybrid'      => ['APO05', 'BAI01', 'BAI03', 'BAI05', 'BAI06'],
        ],
        'DF10' => [
            'first_mover'  => ['APO04', 'APO05', 'BAI03', 'BAI04'],
            'follower'     => ['APO04', 'APO05', 'BAI03'],
            'slow_adopter' => ['APO01', 'APO07', 'APO09', 'BAI03'],
        ],
        'DF11' => [
            'large'  => ['EDM01', 'EDM02', 'APO01', 'APO05', 'APO07', 'APO09', 'BAI01', 'DSS01', 'MEA01'],
            'medium' => ['EDM01', 'APO01', 'APO02', 'APO05', 'APO07', 'BAI01', 'DSS01'],
            'small'  => ['APO01', 'APO07', 'APO09', 'DSS01'],
        ],
    ];

    /**
     * Generate & simpan Governance & Management Objectives
     */
    public function generate(Assessment $assessment)
    {
        $answers = AssessmentDesignFactorAnswer::where('assessment_id', $assessment->id)->get();

        if ($answers->isEmpty()) {
            return collect();
        }

        $objectiveScores = [];

        foreach ($answers as $answer) {
            $designFactor = DesignFactor::find($answer->design_factor_id);

            if (!$designFactor) {
                continue;
            }

            $dfCode = $designFactor->code; // pastikan code = DF01, DF02, ...

            if (!isset($this->mapping[$dfCode])) {
                Log::warning("Mapping tidak ditemukan untuk Design Factor: {$dfCode}");
                continue;
            }

            match ($designFactor->type) {
                'single_choice'   => $this->processSingleChoice($answer, $dfCode, $objectiveScores),
                'rating'          => $this->processRating($answer, $dfCode, $objectiveScores),
                'multiple_choice' => $this->processMultipleChoice($answer, $dfCode, $objectiveScores),
                default           => null,
            };
        }

        return $this->saveObjectives($assessment, $objectiveScores);
    }

    private function processSingleChoice(
        AssessmentDesignFactorAnswer $answer,
        string $dfCode,
        array &$objectiveScores
    ): void {
        if (empty($answer->answer_value)) {
            return;
        }

        $option = DesignFactorOption::find($answer->answer_value);

        if (!$option || empty($option->value)) {
            return;
        }

        $key = $this->normalizeKey($option->value);
        $codes = $this->mapping[$dfCode][$key] ?? [];

        foreach ($codes as $code) {
            $objectiveScores[$code] = ($objectiveScores[$code] ?? 0) + 1;
        }
    }

    private function processRating(
        AssessmentDesignFactorAnswer $answer,
        string $dfCode,
        array &$objectiveScores
    ): void {
        $values = is_array($answer->answer_values) ? $answer->answer_values : [];

        foreach ($values as $optionId => $score) {
            $score = (float) $score;

            // Hanya rating ≥ 4 yang dihitung
            if ($score < 4) {
                continue;
            }

            $option = DesignFactorOption::find($optionId);

            if (!$option || empty($option->value)) {
                continue;
            }

            $key = $this->normalizeKey($option->value);
            $codes = $this->mapping[$dfCode][$key] ?? [];

            foreach ($codes as $code) {
                $objectiveScores[$code] = ($objectiveScores[$code] ?? 0) + $score;
            }
        }
    }

    private function processMultipleChoice(
        AssessmentDesignFactorAnswer $answer,
        string $dfCode,
        array &$objectiveScores
    ): void {
        $optionIds = is_array($answer->answer_values) ? $answer->answer_values : [];

        foreach ($optionIds as $optionId) {
            $option = DesignFactorOption::find($optionId);

            if (!$option || empty($option->value)) {
                continue;
            }

            $key = $this->normalizeKey($option->value);
            $codes = $this->mapping[$dfCode][$key] ?? [];

            foreach ($codes as $code) {
                $objectiveScores[$code] = ($objectiveScores[$code] ?? 0) + 1;
            }
        }
    }

    /**
     * Normalisasi key agar cocok dengan mapping
     * (lowercase + ganti spasi/dash jadi underscore)
     */
    private function normalizeKey(string $value): string
    {
        return strtolower(str_replace([' ', '-'], '_', trim($value)));
    }

    // Simpan hasil ke assessment_objectives dan kembalikan collection hasil
    // private function saveObjectives(Assessment $assessment, array $objectiveScores)
    // {
    //     if (empty($objectiveScores)) {
    //         return collect();
    //     }

    //     $codes = array_keys($objectiveScores);

    //     $cobits = Cobit::whereIn('code', $codes)
    //         ->get()
    //         ->keyBy('code');

    //     $results = collect();

    //     DB::transaction(function () use ($assessment, $objectiveScores, $cobits, &$results) {
    //         // Hapus hasil lama agar generate ulang bersih
    //         AssessmentObjective::where('assessment_id', $assessment->id)->delete();

    //         foreach ($objectiveScores as $code => $score) {
    //             $cobit = $cobits->get($code);

    //             if (!$cobit) {
    //                 Log::warning("Code COBIT tidak ditemukan di tabel cobit: {$code}");
    //                 continue;
    //             }

    //             $priority = $this->calculatePriority($score);

    //             $row = AssessmentObjective::create([
    //                 'assessment_id' => $assessment->id,
    //                 'cobit_id'      => $cobit->id_cobit,
    //                 'code'          => $cobit->code,
    //                 'domain'        => $cobit->domain, // domain dari tabel cobit
    //                 'priority'      => $priority,
    //                 'score'         => round($score, 2),
    //             ]);

    //             // Load relasi agar siap dipakai di view
    //             $row->setRelation('cobit', $cobit);

    //             $results->push($row);
    //         }
    //     });

    //     return $results->sortByDesc('score')->values();
    // }
    private function saveObjectives(Assessment $assessment, array $objectiveScores)
    {
        if (empty($objectiveScores)) {
            return collect();
        }

        // Sort score tertinggi dulu, ambil hanya 5
        arsort($objectiveScores);                       // score descending
        $topScores = array_slice($objectiveScores, 0, 5, true); // top 5

        $codes = array_keys($topScores);

        $cobits = Cobit::whereIn('code', $codes)
            ->get()
            ->keyBy('code');

        $results = collect();

        DB::transaction(function () use ($assessment, $topScores, $cobits, &$results) {
            // Hapus hasil lama agar generate ulang bersih
            AssessmentObjective::where('assessment_id', $assessment->id)->delete();

            foreach ($topScores as $code => $score) {
                $cobit = $cobits->get($code);

                if (!$cobit) {
                    Log::warning("Code COBIT tidak ditemukan di tabel cobit: {$code}");
                    continue;
                }

                $priority = $this->calculatePriority($score);

                $row = AssessmentObjective::create([
                    'assessment_id' => $assessment->id,
                    'cobit_id'      => $cobit->id_cobit,
                    'code'          => $cobit->code,
                    'domain'        => $cobit->domain,
                    'priority'      => $priority,
                    'score'         => round($score, 2),
                ]);

                $row->setRelation('cobit', $cobit);
                $results->push($row);
            }
        });

        return $results->sortByDesc('score')->values();
    }
    // private function saveObjectives(Assessment $assessment, array $objectiveScores)
    // {
    //     if (empty($objectiveScores)) {
    //         return collect();
    //     }

    //     // 1. Hitung priority + kelompokkan
    //     $byPriority = [
    //         'high'   => [],
    //         'medium' => [],
    //         'low'    => [],
    //     ];

    //     foreach ($objectiveScores as $code => $score) {
    //         $priority = $this->calculatePriority($score);
    //         $byPriority[$priority][$code] = $score;
    //     }

    //     // 2. Sort setiap priority dari score tertinggi
    //     foreach ($byPriority as &$group) {
    //         arsort($group);
    //     }
    //     unset($group);

    //     // 3. Ambil kuota per priority (total 10)
    //     $limits = [
    //         'high'   => 4,
    //         'medium' => 3,
    //         'low'    => 3,
    //     ];

    //     $selected = [];

    //     foreach ($limits as $priority => $limit) {
    //         $selected += array_slice($byPriority[$priority], 0, $limit, true);
    //     }

    //     // 4. Kalau total masih < 10 (karena ada priority yang kosong),
    //     //    isi sisa dari objective yang belum terpilih (score tertinggi)
    //     if (count($selected) < 10) {
    //         $remaining = array_diff_key($objectiveScores, $selected);
    //         arsort($remaining);

    //         $need = 10 - count($selected);
    //         $selected += array_slice($remaining, 0, $need, true);
    //     }

    //     // 5. Simpan yang terpilih
    //     $codes = array_keys($selected);

    //     $cobits = Cobit::whereIn('code', $codes)
    //         ->get()
    //         ->keyBy('code');

    //     $results = collect();

    //     DB::transaction(function () use ($assessment, $selected, $cobits, &$results) {
    //         // Hapus hasil lama
    //         AssessmentObjective::where('assessment_id', $assessment->id)->delete();

    //         foreach ($selected as $code => $score) {
    //             $cobit = $cobits->get($code);

    //             if (!$cobit) {
    //                 Log::warning("Code COBIT tidak ditemukan di tabel cobit: {$code}");
    //                 continue;
    //             }

    //             $priority = $this->calculatePriority($score);

    //             $row = AssessmentObjective::create([
    //                 'assessment_id' => $assessment->id,
    //                 'cobit_id'      => $cobit->id_cobit,
    //                 'code'          => $cobit->code,
    //                 'domain'        => $cobit->domain,
    //                 'priority'      => $priority,
    //                 'score'         => round($score, 2),
    //             ]);

    //             $row->setRelation('cobit', $cobit);
    //             $results->push($row);
    //         }
    //     });

    //     return $results->sortByDesc('score')->values();
    // }

    protected function calculatePriority(float $score): string
    {
        if ($score >= 4) {
            return 'high';
        }

        if ($score >= 2) {
            return 'medium';
        }

        return 'low';
    }
}