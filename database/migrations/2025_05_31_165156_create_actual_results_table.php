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
        Schema::create('actual_results', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('completed_tasks');
            $table->unsignedTinyInteger('quality_rating')->nullable();
            $table->unsignedTinyInteger('efficiency_score')->nullable();

            // ✅ المفتاح الخارجي لـ unit_goal_id (الهدف المرتبط بالوحدة)
            $table->unsignedBigInteger('unit_goal_id');
            $table->foreign('unit_goal_id')->references('id')->on('unit_goals')->onDelete('cascade');

            // ✅ unit_id (معرف الوحدة الأم) و department_goal_id (معرف هدف القسم) للتصنيف والتقارير الأسهل
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');

            $table->unsignedBigInteger('department_goal_id')->nullable();
            $table->foreign('department_goal_id')->references('id')->on('department_goals')->onDelete('set null');
            
            $table->timestamps();

            // ✅ قيد فريد لضمان سجل واحد لكل هدف وحدة في تاريخ معين
            $table->unique(['date', 'unit_goal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actual_results');
    }
};
