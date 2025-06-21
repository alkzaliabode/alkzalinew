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
        Schema::create('unit_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('cascade');

            $table->foreignId('department_goal_id')->constrained()->onDelete('cascade');

            // إذا تريد تحتفظ باسم الوحدة:
            $table->string('unit_name');

            $table->text('goal_text');

            // ✅ إضافة عمود 'target_tasks' هنا
            $table->unsignedInteger('target_tasks')->default(0)->comment('عدد المهام المستهدفة'); // مقياس الأهداف

            $table->date('date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_goals');
    }
};
