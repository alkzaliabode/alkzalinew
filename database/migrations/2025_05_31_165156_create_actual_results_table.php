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

            // المفتاح الخارجي مرتبط بجدول unit_goals وليس units
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('unit_goals')->onDelete('cascade');

            $table->timestamps();
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
