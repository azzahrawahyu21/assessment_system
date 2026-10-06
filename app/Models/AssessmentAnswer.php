<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAnswer extends Model
{
    protected $fillable = [
        'assessment_id',
        'respondent_id',
        'cobit_statement_id',
        'answer',
    ];

    protected $casts = [
        'answer' => 'boolean',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function respondent(): BelongsTo
    {
        return $this->belongsTo(AssessmentRespondent::class, 'respondent_id');
    }

    public function statement(): BelongsTo
    {
        return $this->belongsTo(CobitStatement::class, 'cobit_statement_id', 'id_statement');
    }
}