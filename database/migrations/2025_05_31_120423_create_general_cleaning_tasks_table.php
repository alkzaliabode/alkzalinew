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
            $table->date('date'); // التاريخ
            $table->enum('shift', ['صباحي', 'مسائي', 'ليلي']); // الوجبة
            $table->string('task_type'); // نوع المهمة
            $table->string('location'); // الموقع
            // عمود تفاصيل الصيانة
            $table->text('maintenance_details')->nullable(); // تفاصيل الصيانة (مثلاً: ما تم إصلاحه، المشاكل الموجودة)

            // حقول تتبع المستخدم الذي قام بالإنشاء والتعديل
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('معرف المستخدم الذي أنشأ المهمة'); // من قام بإنشاء المهمة
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->comment('معرف المستخدم الذي عدل المهمة آخر مرة'); // من قام بتعديل المهمة آخر مرة

            $table->integer('quantity')->nullable(); // الكمية
            $table->enum('status', ['مكتمل', 'قيد التنفيذ', 'ملغى']); // الحالة
            $table->text('notes')->nullable(); // ملاحظات
            $table->string('responsible_persons')->nullable(); // الأشخاص المسؤولون
            $table->float('working_hours')->nullable(); // ساعات العمل (تم نقلها هنا من ملاحظاتك السابقة)


            // 🔗 روابط الأهداف والتقدم
            $table->foreignId('related_goal_id')->nullable()->constrained('unit_goals')->onDelete('set null'); // الهدف المرتبط
            
            // إضافة عمود unit_id
            $table->unsignedBigInteger('unit_id')->nullable(); // معرف الوحدة
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null'); // مفتاح خارجي للوحدات

            $table->float('progress')->default(0); // نسبة الإنجاز
            $table->integer('result_value')->nullable(); // النتائج المحققة
            $table->json('resources_used')->nullable(); // الموارد المستخدمة (مثل: JSON array of items and quantities)
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending'); // حالة التحقق

            $table->json('before_images')->nullable(); // صور قبل التنفيذ (مسارات في JSON array)
            $table->json('after_images')->nullable(); // صور بعد التنفيذ (مسارات في JSON array)

            // إضافة حقول العدادات (counts)
            $table->integer('mats_count')->default(0); // عدد الحصائر
            $table->integer('pillows_count')->default(0); // عدد الوسائد
            $table->integer('fans_count')->default(0); // عدد المراوح
            $table->integer('windows_count')->default(0); // عدد الشبابيك
            $table->integer('carpets_count')->default(0); // عدد السجاد
            $table->integer('blankets_count')->default(0); // عدد البطانيات
            $table->integer('beds_count')->default(0); // عدد الأسرة
            $table->integer('beneficiaries_count')->default(0); // عدد المستفيدين
            $table->integer('filled_trams_count')->default(0); // عدد عربات المياه المعبأة
            $table->integer('carpets_laid_count')->default(0); // عدد السجاد الذي تم فرشه
            $table->integer('large_containers_count')->default(0); // عدد الحاويات الكبيرة
            $table->integer('small_containers_count')->default(0); // عدد الحاويات الصغيرة

            $table->timestamps(); // أعمدة created_at و updated_at
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
