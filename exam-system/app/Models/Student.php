<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_class_id',  // NEW: Foreign key to school_classes table
        'enrollment_number',
        'class',            // Keep for backward compatibility (string like "10th A" etc.)
        'date_of_birth',
        'address',
        'target_exam',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Relationship: Student belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * NEW: Relationship: Student belongs to a SchoolClass
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    /**
     * Relationship: Student has many parents (many-to-many)
     */
    public function parents()
    {
        return $this->belongsToMany(ParentModel::class, 'parent_student', 'student_id', 'parent_id')
                    ->withTimestamps();
    }

    /**
     * Relationship: Student has many exam attempts
     */
    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * Relationship: Student has many results
     */
    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    /**
     * Relationship: Student can be enrolled in many exams
     */
    public function enrolledExams()
    {
        return $this->belongsToMany(Exam::class, 'exam_students')
                    ->withPivot('is_enrolled')
                    ->withTimestamps();
    }

    /**
     * Check if student has attempted a specific exam
     */
    public function hasAttemptedExam($examId)
    {
        return $this->examAttempts()->where('exam_id', $examId)->exists();
    }

    /**
     * Get active attempt for a specific exam
     */
    public function getActiveAttempt($examId)
    {
        return $this->examAttempts()
                    ->where('exam_id', $examId)
                    ->where('status', 'in_progress')
                    ->first();
    }

    /**
     * NEW: Get class name (uses school_class relationship if available, falls back to class field)
     */
    public function getClassName()
    {
        if ($this->schoolClass) {
            return $this->schoolClass->name . ($this->schoolClass->section ? ' - ' . $this->schoolClass->section : '');
        }
        
        return $this->class ?? 'Not Assigned';
    }

    /**
     * NEW: Check if student belongs to a specific class
     */
    public function belongsToClass($classId)
    {
        return $this->school_class_id == $classId;
    }
}
