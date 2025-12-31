<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('total_marks', 8, 2)->default(0);
            $table->decimal('obtained_marks', 8, 2)->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('unattempted')->default(0);
            $table->integer('marked_for_review')->default(0);
            $table->decimal('accuracy_percentage', 5, 2)->default(0);
            $table->integer('rank')->nullable();
            $table->integer('total_participants')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->unique('attempt_id');
            $table->index(['exam_id', 'student_id']);
        });

        Schema::create('exam_result_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_id')->constrained('exam_results')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->integer('total_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('unattempted')->default(0);
            $table->decimal('marks_obtained', 8, 2)->default(0);
            $table->decimal('accuracy_percentage', 5, 2)->default(0);
            $table->integer('average_time_per_question')->default(0);
            $table->timestamps();
            
            $table->index('result_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_result_subjects');
        Schema::dropIfExists('exam_results');
    }
};
