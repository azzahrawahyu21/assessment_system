<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CobitStatement extends Model
{
    protected $table = 'cobit_statements';
    
    protected $primaryKey = 'id_statement';

    public $incrementing = true;

    protected $fillable = [
        'cobit_id',
        'level',
        'statement',
    ];

    public function cobit(): BelongsTo
    {
        return $this->belongsTo(Cobit::class, 'cobit_id', 'id_cobit');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class, 'cobit_statement_id', 'id_statement');
    }
}