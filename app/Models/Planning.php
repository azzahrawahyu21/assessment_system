<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Planning extends Model
{
    protected $primaryKey = 'id_plan';

    protected $fillable = [
        'no_letter',
        'name',
        'date',
        'period',
        'planning_type_id',
        'objective',
        'target',
        'success_indicator',
        'budget',
        'funding_source',
        'budget_note',
        'document_path',
        'status',
        'created_by',
        'department_id',
    ];

    protected $casts = [
        'date' => 'date',
        'budget' => 'decimal:2',
    ];

    public function planningType(): BelongsTo
    {
        return $this->belongsTo(PlanningType::class, 'planning_type_id', 'id_type');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id_department');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(PlanningApproval::class, 'planning_id', 'id_plan');
    }

    public function implementation(): HasOne
    {
        return $this->hasOne(Implementation::class, 'planning_id', 'id_plan');
    }

    public function implementations(): HasMany
    {
        return $this->hasMany(Implementation::class, 'planning_id', 'id_plan');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRevision($query)
    {
        return $query->where('status', 'revision');
    }

    public function getRouteKeyName(): string
    {
        return 'id_plan';
    }

    public function digitalSignature()
    {
        return $this->hasOne(DigitalSignature::class, 'planning_id', 'id_plan');
    }
}