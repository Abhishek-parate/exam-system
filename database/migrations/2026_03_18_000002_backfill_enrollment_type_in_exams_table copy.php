<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Set all existing exams with NULL enrollment_type to 'open'
        // This fixes exams created before the enrollment_type column was added
        if (Schema::hasColumn('exams', 'enrollment_type')) {
            DB::table('exams')
                ->whereNull('enrollment_type')
                ->update(['enrollment_type' => 'open']);
        }
    }

    public function down(): void
    {
        // Nothing to reverse
    }
};