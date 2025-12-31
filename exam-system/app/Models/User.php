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
        'is_active' => 'boolean',
    ];

    // Relationships
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

    // Helper Methods
    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }

    public function isTeacher()
    {
        return $this->role->name === 'teacher';
    }

    public function isStudent()
    {
        return $this->role->name === 'student';
    }

    public function isParent()
    {
        return $this->role->name === 'parent';
    }

    public function hasPermission($permission)
    {
        return $this->role->permissions()->where('name', $permission)->exists();
    }
}
