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
        Schema::create('sanitation_facility_tasks', function (Blueprint $table) {
            $table->id();

            $table->date('date'); // التاريخ
            $table->enum('shift', ['صباحي', 'مسائي', 'ليلي']); // الوجبة
            $table->string('task_type'); // نوع المهمة
            $table->string('facility_name'); // اسم المرفق الصحي
            $table->text('details'); // تفاصيل المهمة
            $table->enum('status', ['مكتمل', 'قيد التنفيذ', 'ملغى']); // الحالة
            $table->text('notes')->nullable(); // ملاحظات

            // 🔗 روابط الأهداف والتقدم
            $table->foreignId('related_goal_id')->nullable()->constrained('unit_goals')->nullOnDelete();
            $table->float('progress')->default(0); // نسبة الإنجاز
            $table->integer('result_value')->nullable(); // النتائج المحققة
            $table->json('resources_used')->nullable(); // الموارد المستخدمة
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending'); // حالة التحقق

            $table->json('before_images')->nullable(); // صور قبل التنفيذ
            $table->json('after_images')->nullable(); // صور بعد التنفيذ

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanitation_facility_tasks');
    }
};
