<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'exam_code', 'exam_category_id', 'description',
        'duration_minutes', 'total_questions', 'total_marks',
        'randomize_questions', 'randomize_options', 'show_results_immediately',
        'start_time', 'end_time', 'result_release_time',
        'allow_resume', 'is_active', 'enrollment_type', 'created_by',
    ];

    protected $casts = [
        'start_time'               => 'datetime',
        'end_time'                 => 'datetime',
        'result_release_time'      => 'datetime',
        'randomize_questions'      => 'boolean',
        'randomize_options'        => 'boolean',
        'show_results_immediately' => 'boolean',
        'allow_resume'             => 'boolean',
        'is_active'                => 'boolean',
        'total_marks'              => 'decimal:2',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------
    public function examCategory()
    {
        return $this->belongsTo(ExamCategory::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
                    ->withPivot('display_order')
                    ->orderBy('exam_questions.display_order');
    }

    public function markingSchemes()
    {
        return $this->hasMany(ExamMarkingScheme::class);
    }

    public function enrolledStudents()
    {
        return $this->belongsToMany(Student::class, 'exam_students')
                    ->withPivot('is_enrolled')
                    ->withTimestamps();
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    // -------------------------------------------------------
    // ✅ TIMEZONE-SAFE: use now() helper which respects
    // app.timezone from config/app.php
    // -------------------------------------------------------
    private function currentTime(): Carbon
    {
        return now();
    }

    public function isUpcoming(): bool
    {
        return $this->currentTime()->lt($this->start_time);
    }

    public function isOngoing(): bool
    {
        $now = $this->currentTime();
        return $now->gte($this->start_time) && $now->lte($this->end_time);
    }

    public function isExpired(): bool
    {
        return $this->currentTime()->gt($this->end_time);
    }

    public function canBeAttempted(): bool
    {
        return $this->is_active && $this->isOngoing();
    }

    public function areResultsPublished(): bool
    {
        if ($this->show_results_immediately) return true;
        if ($this->result_release_time) {
            return $this->currentTime()->gte($this->result_release_time);
        }
        return false;
    }

    public function getStatusAttribute(): string
    {
        if ($this->isUpcoming()) return 'upcoming';
        if ($this->isOngoing())  return 'ongoing';
        return 'expired';
    }

    // -------------------------------------------------------
    // Enrollment helpers
    // -------------------------------------------------------
    public function isOpenEnrollment(): bool
    {
        // Treat NULL as 'open' for backward compatibility
        return is_null($this->enrollment_type) || $this->enrollment_type === 'open';
    }

    public function isAccessibleByStudent(Student $student): bool
    {
        if ($this->isOpenEnrollment()) return true;

        return $this->enrolledStudents()
                    ->where('student_id', $student->id)
                    ->where('is_enrolled', true)
                    ->exists();
    }
}