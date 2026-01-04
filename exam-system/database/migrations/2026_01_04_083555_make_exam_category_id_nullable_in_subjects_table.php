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
        Schema::table('subjects', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['exam_category_id']);
            
            // Make the column nullable
            $table->unsignedBigInteger('exam_category_id')->nullable()->change();
            
            // Re-add the foreign key
            $table->foreign('exam_category_id')
                  ->references('id')
                  ->on('exam_categories')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['exam_category_id']);
            
            // Make it NOT NULL again
            $table->unsignedBigInteger('exam_category_id')->nullable(false)->change();
            
            // Re-add the foreign key
            $table->foreign('exam_category_id')
                  ->references('id')
                  ->on('exam_categories')
                  ->onDelete('cascade');
        });
    }
};
