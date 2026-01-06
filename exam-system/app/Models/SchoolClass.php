<?php
// app/Models/SchoolClass.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'school_classes';

    protected $fillable = [
        'name',
        'section',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationship: Class has many teachers (many-to-many)
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'class_teacher', 'school_class_id', 'teacher_id')
                    ->withTimestamps();
    }

    // Relationship: Class has many exams
    public function exams()
    {
        return $this->hasMany(Exam::class, 'school_class_id');
    }

    // Relationship: Class has many students
    public function students()
    {
        return $this->hasMany(Student::class, 'school_class_id');
    }
}
