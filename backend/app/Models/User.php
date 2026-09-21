<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Role helpers ──────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    // ── Relationships ─────────────────────────────────────────────

    /** Children this staff member monitors */
    public function assignedChildren()
    {
        return $this->hasMany(Child::class, 'staff_id');
    }

    /** Children linked to this parent */
    public function children()
    {
        return $this->hasMany(Child::class, 'parent_id');
    }

    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_reads', 'user_id', 'notification_id')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'generated_by');
    }
}
