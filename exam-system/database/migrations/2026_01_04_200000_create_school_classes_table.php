<?php
// database/migrations/2026_01_04_200000_create_school_classes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Class 10-A", "Grade 12 Science"
            $table->string('section')->nullable(); // A, B, C, etc.
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table for many-to-many relationship (class <-> teachers)
        Schema::create('class_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['school_class_id', 'teacher_id']);
        });

        // Add class_id to exams table
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('school_class_id')->nullable()->after('exam_category_id')->constrained('school_classes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['school_class_id']);
            $table->dropColumn('school_class_id');
        });
        
        Schema::dropIfExists('class_teacher');
        Schema::dropIfExists('school_classes');
    }
};
