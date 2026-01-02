<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'enrollment_number',
        'class',          // string like "10th A" etc.
        'date_of_birth',
        'address',
        'target_exam',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parents()
    {
        return $this->belongsToMany(ParentModel::class, 'parent_student', 'student_id', 'parent_id')
                    ->withTimestamps();
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function enrolledExams()
    {
        return $this->belongsToMany(Exam::class, 'exam_students')
                    ->withPivot('is_enrolled')
                    ->withTimestamps();
    }

    public function hasAttemptedExam($examId)
    {
        return $this->examAttempts()->where('exam_id', $examId)->exists();
    }

    public function getActiveAttempt($examId)
    {
        return $this->examAttempts()
                    ->where('exam_id', $examId)
                    ->where('status', 'in_progress')
                    ->first();
    }
}
