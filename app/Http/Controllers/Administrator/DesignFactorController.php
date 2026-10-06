<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\DesignFactor;
use App\Models\DesignFactorOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DesignFactorController extends Controller
{
    public function index()
    {
        $designFactors = DesignFactor::with('options')->orderBy('code')->get();
        return response()->json($designFactors);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:design_factors,code|max:20',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:rating,single_choice,multiple_choice',
            'options' => 'nullable|array',
            'options.*.label' => 'required_with:options|string',
            'options.*.value' => 'nullable|string',
            'options.*.order' => 'nullable|integer',
        ]);

        $designFactor = DB::transaction(function () use ($request) {
            $df = DesignFactor::create($request->only(['code', 'name', 'description', 'type']));

            if ($request->filled('options')) {
                foreach ($request->options as $index => $option) {
                    DesignFactorOption::create([
                        'design_factor_id' => $df->id_df,
                        'label' => $option['label'],
                        'value' => $option['value'] ?? null,
                        'order' => $option['order'] ?? $index,
                    ]);
                }
            }

            return $df->load('options');
        });

        return response()->json([
            'message' => 'Design Factor berhasil ditambahkan',
            'data' => $designFactor,
        ], 201);
    }

    public function show($id)
    {
        $designFactor = DesignFactor::with('options')->findOrFail($id);
        return response()->json($designFactor);
    }

    public function update(Request $request, $id)
    {
        $designFactor = DesignFactor::findOrFail($id);

        $request->validate([
            'code' => 'required|string|unique:design_factors,code,' . $id . ',id_df|max:20',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:rating,single_choice,multiple_choice',
        ]);

        $designFactor->update($request->only(['code', 'name', 'description', 'type']));

        return response()->json([
            'message' => 'Design Factor berhasil diupdate',
            'data' => $designFactor->fresh('options'),
        ]);
    }

    public function destroy($id)
    {
        $designFactor = DesignFactor::findOrFail($id);
        $designFactor->delete();

        return response()->json(['message' => 'Design Factor berhasil dihapus']);
    }

    public function storeOption(Request $request, $dfId)
    {
        $request->validate([
            'label' => 'required|string',
            'value' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $option = DesignFactorOption::create([
            'design_factor_id' => $dfId,
            'label' => $request->label,
            'value' => $request->value,
            'order' => $request->order ?? 0,
        ]);

        return response()->json([
            'message' => 'Option berhasil ditambahkan',
            'data' => $option,
        ], 201);
    }

    public function destroyOption($optionId)
    {
        $option = DesignFactorOption::findOrFail($optionId);
        $option->delete();

        return response()->json(['message' => 'Option berhasil dihapus']);
    }
}