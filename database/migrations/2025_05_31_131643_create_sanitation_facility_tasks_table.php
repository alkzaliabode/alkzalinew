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

            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete(); // ✅ تم إضافة ربط للوحدة
            $table->date('date'); // التاريخ
            $table->enum('shift', ['صباحي', 'مسائي', 'ليلي']); // الوجبة
            $table->string('task_type'); // نوع المهمة
            $table->string('facility_name'); // اسم المرفق الصحي
            $table->text('details'); // تفاصيل المهمة
            $table->enum('status', ['مكتمل', 'قيد التنفيذ', 'ملغى']); // الحالة
            $table->text('notes')->nullable(); // ملاحظات

            // ✅ إضافة حقول تتبع المستخدم الذي قام بالإنشاء والتعديل
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('معرف المستخدم الذي أنشأ المهمة'); // من قام بإنشاء المهمة
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->comment('معرف المستخدم الذي عدل المهمة آخر مرة'); // من قام بتعديل المهمة آخر مرة

            // 🔗 روابط الأهداف والتقدم
            $table->foreignId('related_goal_id')->nullable()->constrained('unit_goals')->nullOnDelete();
            $table->float('progress')->default(0); // نسبة الإنجاز
            $table->integer('result_value')->nullable(); // النتائج المحققة
            // تم دمج إضافة عمود resources_used هنا
            $table->json('resources_used')->nullable(); // الموارد المستخدمة
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending'); // حالة التحقق

            $table->json('before_images')->nullable(); // صور قبل التنفيذ
            $table->json('after_images')->nullable(); // صور بعد التنفيذ

            // إضافة حقول العدادات الخاصة بوحدة الصحيات هنا
            $table->integer('seats_count')->default(0);
            $table->integer('mirrors_count')->default(0);
            $table->integer('mixers_count')->default(0);
            $table->integer('doors_count')->default(0);
            $table->integer('sinks_count')->default(0);
            $table->integer('toilets_count')->default(0);
            $table->integer('working_hours')->default(0); // ✅ إضافة ساعات العمل هنا

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
