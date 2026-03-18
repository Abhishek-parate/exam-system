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
        'allow_resume', 'is_active', 'created_by'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'result_release_time' => 'datetime',
        'randomize_questions' => 'boolean',
        'randomize_options' => 'boolean',
        'show_results_immediately' => 'boolean',
        'allow_resume' => 'boolean',
        'is_active' => 'boolean',
        'total_marks' => 'decimal:2',
    ];

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
                    ->orderBy('display_order');
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

    public function isUpcoming()
    {
        return Carbon::now()->lt($this->start_time);
    }

    public function isOngoing()
    {
        return Carbon::now()->between($this->start_time, $this->end_time);
    }

    public function isExpired()
    {
        return Carbon::now()->gt($this->end_time);
    }

    public function canBeAttempted()
    {
        return $this->is_active && $this->isOngoing();
    }

    public function areResultsPublished()
    {
        if ($this->show_results_immediately) {
            return true;
        }
        
        if ($this->result_release_time) {
            return Carbon::now()->gte($this->result_release_time);
        }
        
        return false;
    }

    public function getStatusAttribute()
    {
        if ($this->isUpcoming()) return 'upcoming';
        if ($this->isOngoing()) return 'ongoing';
        return 'expired';
    }
}
