<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImplementationEvidence extends Model
{
    protected $table = 'implementation_evidences';

    protected $fillable = [
        'implementation_id',
        'file_path',
        'file_name',
        'file_type',
    ];

    public function implementation(): BelongsTo
    {
        return $this->belongsTo(Implementation::class, 'implementation_id');
    }
}