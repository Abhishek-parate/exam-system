<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('teachers', function (Blueprint $table) {
            if (!Schema::hasColumn('teachers', 'employee_id')) {
                $table->string('employee_id')->nullable()->after('user_id')->unique();
            }
        });
    }

    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            if (Schema::hasColumn('teachers', 'employee_id')) {
                $table->dropUnique(['employee_id']);
                $table->dropColumn('employee_id');
            }
        });
    }
};
