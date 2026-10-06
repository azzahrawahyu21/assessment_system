<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'department_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id_role');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id_department');
    }

    public function plannings(): HasMany
    {
        return $this->hasMany(Planning::class, 'created_by');
    }

    public function planningApprovals(): HasMany
    {
        return $this->hasMany(PlanningApproval::class, 'approver_id');
    }

    public function implementations(): HasMany
    {
        return $this->hasMany(Implementation::class, 'created_by');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'created_by');
    }

    public function assessmentRespondents(): HasMany
    {
        return $this->hasMany(AssessmentRespondent::class, 'user_id');
    }

    // Helper role checks
    public function isAdministrator(): bool
    {
        return $this->role?->name === 'administrator';
    }

    public function isAssessor(): bool
    {
        return $this->role?->name === 'assessor';
    }

    public function isAdminProdi(): bool
    {
        return $this->role?->name === 'admin_prodi';
    }

    public function isKaprodi(): bool
    {
        return $this->role?->name === 'kaprodi';
    }

    public function isKajur(): bool
    {
        return $this->role?->name === 'kajur';
    }

    public function isWadir(): bool
    {
        return $this->role?->name === 'wadir';
    }

    public function isDirektur(): bool
    {
        return $this->role?->name === 'direktur';
    }

    public function isKeuangan(): bool
    {
        return $this->role?->name === 'keuangan';
    }

    public function isUpa(): bool
    {
        return $this->role?->name === 'upa';
    }

    public function isDosen(): bool
    {
        return $this->role?->name === 'dosen';
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return in_array($this->role?->name, $roles);
    }
}