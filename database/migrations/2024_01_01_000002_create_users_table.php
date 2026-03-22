<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile', 15)->nullable();
            $table->string('password');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['email', 'role_id']);
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('enrollment_number')->unique();
            $table->string('class')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->enum('target_exam', ['JEE', 'NEET', 'MHT-CET'])->nullable();
            $table->timestamps();
            
            $table->index('enrollment_number');
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_code')->unique();
            $table->string('qualification')->nullable();
            $table->text('subjects')->nullable(); // JSON
            $table->timestamps();
        });

        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('relation')->nullable();
            $table->timestamps();
        });

        Schema::create('parent_student', function (Blueprint $table) {
            $table->foreignId('parent_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->primary(['parent_id', 'student_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parent_student');
        Schema::dropIfExists('parents');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('students');
        Schema::dropIfExists('users');
    }
};