<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('attempt_token')->unique();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('auto_submitted_at')->nullable();
            $table->integer('time_taken_seconds')->default(0);
            $table->enum('status', ['in_progress', 'submitted', 'auto_submitted', 'abandoned'])->default('in_progress');
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->unique(['exam_id', 'student_id']);
            $table->index(['exam_id', 'status']);
        });

        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('selected_option_id')->nullable()->constrained('question_options')->onDelete('set null');
            $table->boolean('is_marked_for_review')->default(false);
            $table->integer('time_spent_seconds')->default(0);
            $table->integer('visit_count')->default(0);
            $table->timestamp('first_answered_at')->nullable();
            $table->timestamp('last_answered_at')->nullable();
            $table->timestamps();
            
            $table->unique(['attempt_id', 'question_id']);
            $table->index('attempt_id');
        });

        Schema::create('exam_answer_time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->constrained('exam_answers')->onDelete('cascade');
            $table->timestamp('entered_at');
            $table->timestamp('exited_at')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->timestamps();
            
            $table->index('answer_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_answer_time_logs');
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_attempts');
    }
};
