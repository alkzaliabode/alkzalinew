<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_goals', function (Blueprint $table) {
            if (!Schema::hasColumn('unit_goals', 'unit_id')) {
                $table->foreignId('unit_id')
                    ->after('department_goal_id')
                    ->constrained('units')
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('unit_goals', function (Blueprint $table) {
            if (Schema::hasColumn('unit_goals', 'unit_id')) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            }
        });
    }
};