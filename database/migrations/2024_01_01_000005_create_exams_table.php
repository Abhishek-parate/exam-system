<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // 'open'     = all active students can attempt
            // 'enrolled' = only admin-enrolled students can attempt
            $table->enum('enrollment_type', ['open', 'enrolled'])
                  ->default('enrolled')
                  ->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('enrollment_type');
        });
    }
};