<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    protected $fillable = [
        'name',
        'department_id',
        'is_all_department',
        'status',
        'created_by',
        'started_at',
        'closed_at',
    ];

    protected $casts = [
        'is_all_department' => 'boolean',
        'started_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id_department');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function designFactorAnswers(): HasMany
    {
        return $this->hasMany(AssessmentDesignFactorAnswer::class, 'assessment_id');
    }

    public function objectives(): HasMany
    {
        return $this->hasMany(AssessmentObjective::class, 'assessment_id');
    }

    public function respondents(): HasMany
    {
        return $this->hasMany(AssessmentRespondent::class, 'assessment_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class, 'assessment_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(AssessmentResult::class, 'assessment_id');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function assessmentObjectives()
    {
        return $this->hasMany(AssessmentObjective::class, 'assessment_id');
    }

    public function objectivePriorities()
    {
        return $this->assessmentObjectives();
    }

    public function judgments()
    {
        return $this->hasMany(AssessmentJudgment::class);
    }
}