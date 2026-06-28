<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'status',
        'last_login_at',
        'require_password_change',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'       => 'datetime',
        'password'                => 'hashed',
        'last_login_at'           => 'datetime',
        'require_password_change' => 'boolean',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function checklists()
    {
        return $this->hasMany(FlowerChecklist::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ─── Role Helpers ──────────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'super_admin' || $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }
}
