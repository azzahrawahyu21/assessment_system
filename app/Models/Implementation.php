<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Implementation extends Model
{
    protected $fillable = [
        'planning_id',
        'date',
        'result',
        'obstacle',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function planning(): BelongsTo
    {
        return $this->belongsTo(Planning::class, 'planning_id', 'id_plan');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(ImplementationEvidence::class, 'implementation_id');
    }
}