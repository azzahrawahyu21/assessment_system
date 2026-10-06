<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentObjective extends Model
{
    protected $fillable = [
        'assessment_id',
        'cobit_id',
        'code',
        'domain',
        'priority',
        'score',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function cobit(): BelongsTo
    {
        return $this->belongsTo(Cobit::class, 'cobit_id', 'id_cobit');
    }
}