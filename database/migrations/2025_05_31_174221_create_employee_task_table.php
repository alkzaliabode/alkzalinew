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
        Schema::create('employee_task', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // العلاقة مع الموظف
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('الموظف المنفذ للمهمة');

            // العلاقة مع مهمة النظافة العامة
            $table->foreignId('general_cleaning_task_id')
                ->nullable()
                ->constrained('general_cleaning_tasks')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('مهمة النظافة العامة (nullable)');

            // العلاقة مع مهمة المنشآت الصحية
            $table->foreignId('sanitation_facility_task_id')
                ->nullable()
                ->constrained('sanitation_facility_tasks')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('مهمة المنشآت الصحية (nullable)');

            // تقييم الأداء
            $table->unsignedTinyInteger('employee_rating')
                ->nullable()
                ->comment('تقييم أداء الموظف من 1 إلى 5 نجوم');

            // بيانات إضافية
            $table->time('start_time')->nullable()->comment('وقت بدء المهمة');
            $table->time('end_time')->nullable()->comment('وقت انتهاء المهمة');
            $table->text('notes')->nullable()->comment('ملاحظات المشرف');

            // مفاتيح فريدة لمنع التكرار
            $table->unique(['employee_id', 'general_cleaning_task_id', 'sanitation_facility_task_id'], 'employee_task_unique');

            // فهارس لتحسين الأداء
            $table->index('employee_rating');
            $table->index(['start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_task');
    }
};