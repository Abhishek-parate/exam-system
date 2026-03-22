<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixCheatColumnsExamAttempts extends Migration
{
    public function up(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {

            if (!Schema::hasColumn('exam_attempts', 'tab_switch_count')) {
                $table->integer('tab_switch_count')->default(0);
            }

            if (!Schema::hasColumn('exam_attempts', 'fullscreen_exit_count')) {
                $table->integer('fullscreen_exit_count')->default(0);
            }

            if (!Schema::hasColumn('exam_attempts', 'copy_attempts')) {
                $table->integer('copy_attempts')->default(0);
            }

        });
    }

    public function down(): void {}
}