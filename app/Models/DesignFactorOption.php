<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignFactorOption extends Model
{
    protected $fillable = [
        'design_factor_id',
        'label',
        'value',
        'order',
    ];

    public function designFactor(): BelongsTo
    {
        return $this->belongsTo(DesignFactor::class, 'design_factor_id', 'id_df');
    }
}