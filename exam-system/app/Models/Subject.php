<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_category_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: Subject belongs to ExamCategory
     */
    public function examCategory()
    {
        return $this->belongsTo(ExamCategory::class, 'exam_category_id');
    }

    /**
     * Relationship: Subject has many Chapters
     */
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    /**
     * Relationship: Subject has many Questions
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Relationship: Subject belongs to many Teachers
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject', 'subject_id', 'teacher_id');
    }
}
