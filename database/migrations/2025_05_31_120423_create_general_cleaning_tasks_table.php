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
        Schema::create('general_cleaning_tasks', function (Blueprint $table) {
           $table->id();
    $table->date('date');
    $table->enum('shift', ['صباحي', 'مسائي', 'ليلي']);
    $table->string('task_type');
    $table->string('location');
    $table->integer('quantity')->nullable();
    $table->enum('status', ['مكتمل', 'قيد التنفيذ', 'ملغى']);
    $table->text('notes')->nullable();
    $table->string('responsible_persons')->nullable();

    // 🔗 روابط الأهداف والتقدم
    $table->foreignId('related_goal_id')->nullable()->constrained('unit_goals');
    $table->float('progress')->default(0); // نسبة الإنجاز
    $table->integer('result_value')->nullable(); // النتائج المحققة
    $table->json('resources_used')->nullable(); // الموارد المستخدمة
    $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');

    $table->json('before_images')->nullable();
    $table->json('after_images')->nullable();

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_cleaning_tasks');
    }
};
