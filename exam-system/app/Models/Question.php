// app/Models/Question.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_category_id', 'subject_id', 'chapter_id', 'topic_id',
        'difficulty_id', 'question_text', 'question_image', 
        'marks', 'negative_marks', 'explanation', 'explanation_image',
        'is_active', 'created_by'
    ];

    protected $casts = [
        'marks' => 'decimal:2',
        'negative_marks' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function examCategory()
    {
        return $this->belongsTo(ExamCategory::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function difficulty()
    {
        return $this->belongsTo(QuestionDifficulty::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function correctOption()
    {
        return $this->hasOne(QuestionOption::class)->where('is_correct', true);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope for active questions
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
