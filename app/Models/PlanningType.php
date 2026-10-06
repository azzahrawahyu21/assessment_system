<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanningType extends Model
{
    protected $primaryKey = 'id_type';

    protected $fillable = [
        'name',
        'description',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_type';
    }

    public function plannings(): HasMany
    {
        return $this->hasMany(Planning::class, 'planning_type_id', 'id_type');
    }
}