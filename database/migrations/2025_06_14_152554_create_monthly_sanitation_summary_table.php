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
        Schema::create('monthly_sanitation_summary', function (Blueprint $table) {
            $table->string('id')->primary(); // ID مركب من الشهر + المنشأة + المهمة
            $table->string('month', 7);
            $table->string('facility_name');
            $table->string('task_type');
            $table->integer('total_seats')->default(0);
            $table->integer('total_mirrors')->default(0);
            $table->integer('total_mixers')->default(0);
            $table->integer('total_doors')->default(0);
            $table->integer('total_sinks')->default(0);
            $table->integer('total_toilets')->default(0);
            $table->integer('total_tasks')->default(0);
            $table->timestamps(); // ✅ إضافة created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_sanitation_summary');
    }
};
