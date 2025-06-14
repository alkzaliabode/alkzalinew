<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actual_results', function (Blueprint $table) {
            $table->dropForeign(['unit_id']); // حذف المفتاح الأجنبي القديم

            $table->foreign('unit_id') // إعادة إنشائه بشكل صحيح
                ->references('id')
                ->on('units')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('actual_results', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);

            $table->foreign('unit_id')
                ->references('id')
                ->on('units');
        });
    }
};