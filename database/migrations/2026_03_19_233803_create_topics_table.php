<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    // Drop old topics table if it has wrong structure, recreate with new schema
    Schema::dropIfExists('topics');

    Schema::create('topics', function (Blueprint $table) {
        $table->id();
        $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');
        $table->string('name');
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('topics');
}

};