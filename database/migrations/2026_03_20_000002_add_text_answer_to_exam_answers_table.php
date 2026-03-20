<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            // Only add if column does not already exist (safe to re-run)
            if (!Schema::hasColumn('exam_answers', 'text_answer')) {
                $table->text('text_answer')->nullable()->after('selected_option_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            if (Schema::hasColumn('exam_answers', 'text_answer')) {
                $table->dropColumn('text_answer');
            }
        });
    }
};