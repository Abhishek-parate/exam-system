<?php
// database/migrations/xxxx_xx_xx_add_assigned_teacher_id_to_exams_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('assigned_teacher_id')->nullable()->after('created_by')->constrained('teachers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['assigned_teacher_id']);
            $table->dropColumn('assigned_teacher_id');
        });
    }
};
