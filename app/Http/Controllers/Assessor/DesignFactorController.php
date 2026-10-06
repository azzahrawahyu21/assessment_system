<?php

namespace App\Http\Controllers\Assessor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentDesignFactorAnswer;
use App\Models\Cobit;
use App\Models\Department;
use App\Models\DesignFactor;
use App\Models\DesignFactorOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DesignFactorController extends Controller
{
    public function index(Request $request)
    {
        $query = Assessment::with(['department'])
            ->withCount('designFactorAnswers')
            ->where('created_by', Auth::id())
            ->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('department_id')) {
            $deptId = $request->department_id;
            $query->where(function ($q) use ($deptId) {
                $q->where('department_id', $deptId)
                  ->orWhere('is_all_department', true);
            });
        }

        if ($request->filled('assessment_id')) {
            $query->where('id', $request->assessment_id);
        }

        $assessments = $query->paginate(10)->withQueryString();

        // Samakan atribut yang dipakai view index
        $assessments->getCollection()->transform(function ($a) {
            $a->is_all_departments = (bool) $a->is_all_department;
            return $a;
        });

        $departments = Department::orderBy('name')->get();

        return view('assessor.design-factor.index', compact('assessments', 'departments'));
    }

    public function create()
    {
        return view('assessor.design-factor.create');
    }

    /**
     * Simpan jawaban Design Factor.
     * Model Answer hanya punya: assessment_id, design_factor_id, answer_value, answer_values
     * Jadi 1 baris per Design Factor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'assessment_id'   => ['required', 'integer', 'exists:assessments,id'], // sesuaikan nama tabel jika beda
            'answers'         => ['nullable', 'array'],
            'answers.*'       => ['nullable', 'numeric', 'min:0', 'max:5'],
            'single_choice'   => ['nullable', 'array'],
            'single_choice.*' => ['nullable', 'integer'],
        ]);

        $assessment = Assessment::findOrFail($request->assessment_id);
        abort_if($assessment->created_by !== Auth::id(), 403);

        DB::transaction(function () use ($request, $assessment) {

            // ----- 1. Single Choice -----
            // Form: single_choice[df_id] = option_id
            foreach ($request->input('single_choice', []) as $dfId => $optionId) {
                if (empty($optionId)) {
                    continue;
                }

                // Pastikan option milik DF tersebut
                $option = DesignFactorOption::where('id', $optionId)
                    ->where('design_factor_id', $dfId)
                    ->first();

                if (!$option) {
                    continue;
                }

                AssessmentDesignFactorAnswer::updateOrCreate(
                    [
                        'assessment_id'    => $assessment->id,
                        'design_factor_id' => $dfId,
                    ],
                    [
                        'answer_value'  => (string) $optionId, // simpan option_id sebagai scalar
                        'answer_values' => null,
                    ]
                );
            }

            // ----- 2. Rating & Multiple Choice -----
            // Form answers[option_id] = value (1 untuk checkbox, 1-5 untuk rating)
            $submitted = collect($request->input('answers', []))
                ->filter(fn ($v) => $v !== null && $v !== '');

            if ($submitted->isEmpty()) {
                return;
            }

            $optionIds = $submitted->keys()->map(fn ($id) => (int) $id)->all();
            $options   = DesignFactorOption::whereIn('id', $optionIds)
                ->get()
                ->groupBy('design_factor_id');

            foreach ($options as $dfId => $opts) {
                $df = DesignFactor::find($dfId);
                if (!$df) {
                    continue;
                }

                $type = $df->type; // 'single_choice' | 'multiple_choice' | 'rating'

                if ($type === 'multiple_choice') {
                    // Kumpulkan option_id yang dicentang
                    $selectedIds = $opts->pluck('id')
                        ->filter(fn ($id) => $submitted->has($id))
                        ->values()
                        ->all();

                    AssessmentDesignFactorAnswer::updateOrCreate(
                        [
                            'assessment_id'    => $assessment->id,
                            'design_factor_id' => $dfId,
                        ],
                        [
                            'answer_value'  => null,
                            'answer_values' => $selectedIds,
                        ]
                    );
                } elseif ($type === 'rating') {
                    // Map option_id => score
                    $scores = [];
                    foreach ($opts as $opt) {
                        if ($submitted->has($opt->id)) {
                            $scores[(string) $opt->id] = (float) $submitted->get($opt->id);
                        }
                    }

                    AssessmentDesignFactorAnswer::updateOrCreate(
                        [
                            'assessment_id'    => $assessment->id,
                            'design_factor_id' => $dfId,
                        ],
                        [
                            'answer_value'  => null,
                            'answer_values' => $scores,
                        ]
                    );
                }
            }
        });

        return redirect()
            ->route('assessor.design-factors.index')
            ->with('success', 'Design Factor berhasil disimpan.');
    }

    public function show(Assessment $assessment)
    {
        abort_if($assessment->created_by !== Auth::id(), 403);

        $assessment->load('department');

        // Ambil DF aktif (asumsi kolom is_active ada; jika tidak, hapus where)
        $designFactors = DesignFactor::with(['options' => function ($q) {
                $q->orderBy('order');
            }])
            ->orderBy('id_df') // atau kolom sort yang ada
            ->get()
            ->map(function ($df) {
                // Samakan dengan yang diharapkan view
                $df->id          = $df->id_df;
                $df->input_type  = $df->type; // single_choice / multiple_choice / rating
                $df->options->transform(function ($opt) {
                    $opt->name        = $opt->label;
                    $opt->description = $opt->description ?? null; // jika kolom tidak ada, null
                    $opt->code        = $opt->code ?? null;
                    return $opt;
                });
                return $df;
            });

        // Bangun $answers keyed by design_factor_option_id (yang diharapkan view show)
        $rawAnswers = AssessmentDesignFactorAnswer::where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('design_factor_id');

        $answers = collect();

        foreach ($rawAnswers as $dfId => $row) {
            $df = $designFactors->firstWhere('id_df', $dfId);
            if (!$df) {
                continue;
            }

            if ($df->type === 'single_choice' && $row->answer_value) {
                $optId = (int) $row->answer_value;
                // Object palsu supaya isset($answers[$optId]) true & punya ->value
                $answers[$optId] = (object) [
                    'design_factor_option_id' => $optId,
                    'value'                  => 1,
                ];
            } elseif ($df->type === 'multiple_choice' && is_array($row->answer_values)) {
                foreach ($row->answer_values as $optId) {
                    $answers[(int) $optId] = (object) [
                        'design_factor_option_id' => (int) $optId,
                        'value'                  => 1,
                    ];
                }
            } elseif ($df->type === 'rating' && is_array($row->answer_values)) {
                foreach ($row->answer_values as $optId => $score) {
                    $answers[(int) $optId] = (object) [
                        'design_factor_option_id' => (int) $optId,
                        'value'                  => $score,
                    ];
                }
            }
        }

        $domains = Cobit::orderBy('code')->get(); // sesuaikan kolom sort

        return view('assessor.design-factor.show', compact(
            'assessment',
            'domains',
            'designFactors',
            'answers'
        ));
    }

    public function result(Assessment $assessment)
    {
        abort_if($assessment->created_by !== Auth::id(), 403);

        $assessment->load('department');

        $designFactors = DesignFactor::with(['options' => function ($q) {
                $q->orderBy('order');
            }])
            ->orderBy('id_df')
            ->get()
            ->map(function ($df) {
                $df->id         = $df->id_df;
                $df->input_type = $df->type;
                $df->options->transform(function ($opt) {
                    $opt->name        = $opt->label;
                    $opt->description = $opt->description ?? null;
                    $opt->code        = $opt->code ?? null;
                    return $opt;
                });
                return $df;
            });

        $rawAnswers = AssessmentDesignFactorAnswer::where('assessment_id', $assessment->id)
            ->get()
            ->keyBy('design_factor_id');

        if ($rawAnswers->isEmpty()) {
            return redirect()
                ->route('assessor.design-factors.show', $assessment)
                ->with('error', 'Design Factor belum diisi.');
        }

        // Bangun $answers groupBy design_factor_id, tiap item punya ->option & ->value
        $answers = collect();

        foreach ($rawAnswers as $dfId => $row) {
            $df = $designFactors->firstWhere('id_df', $dfId);
            if (!$df) {
                continue;
            }

            $items = collect();

            if ($df->type === 'single_choice' && $row->answer_value) {
                $opt = $df->options->firstWhere('id', (int) $row->answer_value);
                if ($opt) {
                    $items->push((object) [
                        'option' => $opt,
                        'value'  => 1,
                    ]);
                }
            } elseif ($df->type === 'multiple_choice' && is_array($row->answer_values)) {
                foreach ($row->answer_values as $optId) {
                    $opt = $df->options->firstWhere('id', (int) $optId);
                    if ($opt) {
                        $items->push((object) [
                            'option' => $opt,
                            'value'  => 1,
                        ]);
                    }
                }
            } elseif ($df->type === 'rating' && is_array($row->answer_values)) {
                foreach ($row->answer_values as $optId => $score) {
                    $opt = $df->options->firstWhere('id', (int) $optId);
                    if ($opt) {
                        $items->push((object) [
                            'option' => $opt,
                            'value'  => $score,
                        ]);
                    }
                }
            }

            $answers->put($df->id_df, $items);
            // view memakai $answers->get($df->id) → karena kita set $df->id = id_df, cocok
            $answers->put($df->id, $items);
        }

        return view('assessor.design-factor.result', compact(
            'assessment',
            'designFactors',
            'answers'
        ));
    }

    public function edit(DesignFactor $designFactor)
    {
        // Route model binding default pakai id, sementara PK = id_df
        // Pastikan di RouteServiceProvider / boot:
        // Route::bind('designFactor', fn ($v) => DesignFactor::where('id_df', $v)->firstOrFail());
        $designFactor->load(['options' => fn ($q) => $q->orderBy('order')]);

        return view('assessor.design-factors.edit', compact('designFactor'));
    }

    public function update(Request $request, DesignFactor $designFactor)
    {
        $validated = $request->validate([
            'code'        => ['required', 'string', 'max:10', 'unique:design_factors,code,' . $designFactor->id_df . ',id_df'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type'        => ['required', 'in:single_choice,multiple_choice,rating'], // pakai kolom model
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $designFactor->update([
            'code'        => $validated['code'],
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type'        => $validated['type'],
            // kolom lain hanya jika benar-benar ada di tabel
        ]);

        return redirect()
            ->route('assessor.design-factors.index')
            ->with('success', 'Design Factor berhasil diperbarui.');
    }
}