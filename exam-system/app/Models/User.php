<?php
// app/Models/User.php

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
        'role_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
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
        return $this->role && $this->role->name === 'admin';
    }

    public function isTeacher()
    {
        return $this->role && $this->role->name === 'teacher';
    }

    public function isStudent()
    {
        return $this->role && $this->role->name === 'student';
    }

    public function isParent()
    {
        return $this->role && $this->role->name === 'parent';
    }

    public function getRoleName()
    {
        return $this->role ? $this->role->display_name : 'No Role';
    }

    public function hasPermission($permission)
    {
        return $this->role && $this->role->permissions()->where('name', $permission)->exists();
    }
}
