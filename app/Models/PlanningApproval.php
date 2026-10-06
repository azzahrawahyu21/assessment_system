<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanningApproval extends Model
{
    protected $fillable = [
        'planning_id',
        'approver_id',
        'status',
        'note',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function planning(): BelongsTo
    {
        return $this->belongsTo(Planning::class, 'planning_id', 'id_plan');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}