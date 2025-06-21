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
        Schema::create('monthly_general_cleaning_summary', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('month', 7);
            $table->string('location');
            $table->string('task_type');
            $table->integer('total_mats')->default(0);
            $table->integer('total_pillows')->default(0);
            $table->integer('total_fans')->default(0);
            $table->integer('total_windows')->default(0);
            $table->integer('total_carpets')->default(0);
            $table->integer('total_blankets')->default(0);
            $table->integer('total_beds')->default(0);
            $table->integer('total_beneficiaries')->default(0);
            $table->integer('total_trams')->default(0);
            $table->integer('total_laid_carpets')->default(0);
            $table->integer('total_large_containers')->default(0);
            $table->integer('total_small_containers')->default(0);
            // ✅ إضافة عمود total_tasks هنا
            $table->integer('total_tasks')->default(0);
            $table->timestamps(); // ✅ إضافة created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_general_cleaning_summary');
    }
};
