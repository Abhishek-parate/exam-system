<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Restore all soft-deleted exams that were accidentally deleted
        // due to the nested form bug in edit.blade.php
        DB::table('exams')
            ->whereNotNull('deleted_at')
            ->update(['deleted_at' => null]);
    }

    public function down(): void
    {
        // Nothing to reverse
    }
};