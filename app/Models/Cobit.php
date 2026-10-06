<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cobit extends Model
{
    protected $primaryKey = 'id_cobit';

    public $incrementing = true;

    protected $fillable = [
        'code',
        'domain',
        'description',
    ];

    /** Supaya {cobit} di URL memakai id_cobit */
    public function getRouteKeyName(): string
    {
        return 'id_cobit';
    }

    public function statements(): HasMany
    {
        return $this->hasMany(CobitStatement::class, 'cobit_id', 'id_cobit');
    }

    public function assessmentObjectives(): HasMany
    {
        return $this->hasMany(AssessmentObjective::class, 'cobit_id', 'id_cobit');
    }

    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class, 'cobit_id', 'id_cobit');
    }
}