<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_difficulties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level')->unique();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('chapter_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('topic_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('difficulty_id')->constrained('question_difficulties')->onDelete('cascade');
            $table->text('question_text');
            $table->string('question_image')->nullable();
            $table->decimal('marks', 5, 2)->default(4.00);
            $table->decimal('negative_marks', 5, 2)->default(1.00);
            $table->text('explanation')->nullable();
            $table->string('explanation_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['exam_category_id', 'subject_id', 'difficulty_id']);
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->char('option_key', 1);
            $table->text('option_text');
            $table->string('option_image')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
            
            $table->index('question_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_difficulties');
    }
};
