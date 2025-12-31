<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('exam_code')->unique();
            $table->foreignId('exam_category_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->integer('duration_minutes');
            $table->integer('total_questions');
            $table->decimal('total_marks', 8, 2);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_options')->default(false);
            $table->boolean('show_results_immediately')->default(false);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->dateTime('result_release_time')->nullable();
            $table->boolean('allow_resume')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['exam_category_id', 'start_time', 'end_time']);
        });

        Schema::create('exam_marking_schemes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('correct_marks', 5, 2)->default(4.00);
            $table->decimal('wrong_marks', 5, 2)->default(1.00);
            $table->decimal('unattempted_marks', 5, 2)->default(0.00);
            $table->timestamps();
            
            $table->unique(['exam_id', 'subject_id']);
        });

        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->integer('display_order');
            $table->timestamps();
            
            $table->unique(['exam_id', 'question_id']);
            $table->index('exam_id');
        });

        Schema::create('exam_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->boolean('is_enrolled')->default(true);
            $table->timestamps();
            
            $table->unique(['exam_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_students');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exam_marking_schemes');
        Schema::dropIfExists('exams');
    }
};
