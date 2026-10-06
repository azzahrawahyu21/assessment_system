<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentJudgment extends Model
{
    protected $fillable = [
        'assessment_id',
        'cobit_id',
        'recommended_level',
        'activities',
        'achieved_level',
        'evidence_path',
        'evidence_paths',
        'notes',
    ];

    protected $casts = [
        'recommended_level' => 'integer',
        'achieved_level'    => 'integer',
        'activities'        => 'array',
        'evidence_paths'    => 'array',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function cobit(): BelongsTo
    {
        return $this->belongsTo(
            Cobit::class,
            'cobit_id',
            'id_cobit'
        );
    }

    public function getGapAttribute(): ?int
    {
        if ($this->recommended_level === null || $this->achieved_level === null) {
            return null;
        }

        return $this->achieved_level - $this->recommended_level;
    }
}