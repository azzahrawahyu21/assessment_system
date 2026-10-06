<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DesignFactor extends Model
{
    protected $primaryKey = 'id_df';

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(DesignFactorOption::class, 'design_factor_id', 'id_df')->orderBy('order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentDesignFactorAnswer::class, 'design_factor_id', 'id_df');
    }
}