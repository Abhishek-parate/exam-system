<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
        });

        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('chapter_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('topics');
        Schema::dropIfExists('chapters');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('exam_categories');
    }
};
