<?php
// app/Models/Teacher.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'employee_code',
        'phone',
        'address',
        'qualifications',
    ];

    protected $casts = [
        'qualifications' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'created_by', 'user_id');
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_teacher', 'teacher_id', 'school_class_id')
                    ->withTimestamps();
    }

    // Subjects assigned to teacher
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject', 'teacher_id', 'subject_id')
                    ->withTimestamps();
    }

    public function getDisplayName()
    {
        return $this->user->name . ($this->employee_code ? ' (' . $this->employee_code . ')' : '');
    }

    public function isAssignedToClass($classId)
    {
        return $this->classes()->where('school_classes.id', $classId)->exists();
    }

    // Check if teacher is assigned to a subject
    public function hasSubject($subjectId)
    {
        return $this->subjects()->where('subjects.id', $subjectId)->exists();
    }
}
