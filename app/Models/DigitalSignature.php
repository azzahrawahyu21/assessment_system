<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalSignature extends Model
{
    protected $fillable = [
        'planning_id',
        'document_number',
        'document_date',
        'token',
        'hash',
        'director_name',
        'director_signed_at',
        'wadir_name',        // ← bukan wadir1_name
        'wadir_signed_at',   // ← bukan wadir1_signed_at
        'status',
        'generated_by',
    ];

    protected $casts = [
        'document_date'      => 'date',
        'director_signed_at' => 'datetime',
        'wadir_signed_at'    => 'datetime',
    ];

    public function planning()
    {
        return $this->belongsTo(Planning::class, 'planning_id', 'id_plan');
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getVerificationUrlAttribute(): string
    {
        return route('signature.verify', $this->token);
    }
}