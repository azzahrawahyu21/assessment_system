<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $primaryKey = 'id_department';

    protected $fillable = [
        'name',
        'type',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id', 'id_department');
    }

    public function plannings(): HasMany
    {
        return $this->hasMany(Planning::class, 'department_id', 'id_department');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'department_id', 'id_department');
    }
}