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
        'name', 'email', 'mobile', 'password', 'role_id', 'is_active', 'email_verified_at'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
        // ✅ Cast role_id to integer for consistent comparisons
        'role_id'           => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function parent()
    {
        return $this->hasOne(ParentModel::class);
    }

    // ── Helper Methods ─────────────────────────────────────────

    /**
     * Get the role name safely.
     * ✅ FIX: Previously $this->role->name would throw a fatal error if the
     * role relationship returned null (e.g. role_id is null or the roles
     * table row is missing). Laravel silently catches the exception during
     * a request and redirects back to /login — appearing as a login loop
     * with no visible error message.
     */
    private function getRoleName(): ?string
    {
        return $this->role?->name;
    }

    public function isAdmin(): bool
    {
        return $this->getRoleName() === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->getRoleName() === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->getRoleName() === 'student';
    }

    public function isParent(): bool
    {
        return $this->getRoleName() === 'parent';
    }

    public function hasPermission($permission): bool
    {
        if (! $this->role) {
            return false;
        }

        return $this->role->permissions()->where('name', $permission)->exists();
    }
}