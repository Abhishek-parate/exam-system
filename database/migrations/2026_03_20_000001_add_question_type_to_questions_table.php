<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Only add if column does not already exist (safe to re-run)
            if (!Schema::hasColumn('questions', 'question_type')) {
                $table->string('question_type')->default('mcq')->after('is_active');
            }
            if (!Schema::hasColumn('questions', 'correct_answer')) {
                $table->text('correct_answer')->nullable()->after('question_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'correct_answer')) {
                $table->dropColumn('correct_answer');
            }
            if (Schema::hasColumn('questions', 'question_type')) {
                $table->dropColumn('question_type');
            }
        });
    }
};