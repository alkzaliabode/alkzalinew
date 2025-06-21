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
        // إضافة عمود working_hours إلى جدول general_cleaning_tasks
        Schema::table('general_cleaning_tasks', function (Blueprint $table) {
            // التحقق إذا كان العمود موجودًا بالفعل لتجنب الأخطاء عند إعادة التشغيل
            if (!Schema::hasColumn('general_cleaning_tasks', 'working_hours')) {
                // تم التعديل هنا: استخدام decimal()->unsigned() بدلاً من unsignedDecimal()
                $table->decimal('working_hours', 8, 2)->unsigned()->nullable()->after('maintenance_details');
            }
        });

        // إضافة عمود working_hours إلى جدول sanitation_facility_tasks
        // تأكد أن هذا الجدول موجود في قاعدة البيانات أو سيتم إنشاؤه بهجرة أخرى.
        // إذا لم يكن موجودًا، ستفشل هذه الجزئية من الهجرة.
        Schema::table('sanitation_facility_tasks', function (Blueprint $table) {
            // التحقق إذا كان العمود موجودًا بالفعل لتجنب الأخطاء عند إعادة التشغيل
            if (!Schema::hasColumn('sanitation_facility_tasks', 'working_hours')) {
                // تم التعديل هنا: استخدام decimal()->unsigned() بدلاً من unsignedDecimal()
                $table->decimal('working_hours', 8, 2)->unsigned()->nullable()->after('task_type'); // مثال: بعد 'task_type'
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إزالة عمود working_hours من general_cleaning_tasks
        Schema::table('general_cleaning_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('general_cleaning_tasks', 'working_hours')) {
                $table->dropColumn('working_hours');
            }
        });

        // إزالة عمود working_hours من sanitation_facility_tasks
        Schema::table('sanitation_facility_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('sanitation_facility_tasks', 'working_hours')) {
                $table->dropColumn('working_hours');
            }
        });
    }
};
