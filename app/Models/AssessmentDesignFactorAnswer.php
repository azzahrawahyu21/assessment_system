<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentDesignFactorAnswer extends Model
{
    protected $fillable = [
        'assessment_id',
        'design_factor_id',
        'answer_value',
        'answer_values',
    ];

    protected $casts = [
        'answer_values' => 'array',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(
            Assessment::class,
            'assessment_id'
        );
    }

    public function designFactor(): BelongsTo
    {
        return $this->belongsTo(
            DesignFactor::class,
            'design_factor_id',
            'id_df'
        );
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(
            DesignFactorOption::class,
            'answer_value',
            'id'
        );
    }
}