// app/Models/ExamAnswer.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id', 'question_id', 'selected_option_id',
        'is_marked_for_review', 'time_spent_seconds', 'visit_count',
        'first_answered_at', 'last_answered_at'
    ];

    protected $casts = [
        'is_marked_for_review' => 'boolean',
        'first_answered_at' => 'datetime',
        'last_answered_at' => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(ExamAnswerTimeLog::class, 'answer_id');
    }

    public function isCorrect()
    {
        if (!$this->selected_option_id) {
            return false;
        }
        
        return $this->selectedOption->is_correct;
    }

    public function isAttempted()
    {
        return $this->selected_option_id !== null;
    }

    public function getStatusAttribute()
    {
        if ($this->is_marked_for_review) {
            return 'review';
        }
        
        if ($this->isAttempted()) {
            return 'attempted';
        }
        
        return 'not_attempted';
    }
}
