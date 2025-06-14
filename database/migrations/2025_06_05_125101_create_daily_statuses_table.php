<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyStatusesTable extends Migration
{
    public function up(): void
    {
        Schema::create('daily_statuses', function (Blueprint $table) {
            $table->id();

            $table->date('date');                      // التاريخ الميلادي
            $table->string('hijri_date')->nullable();  // التاريخ الهجري
            $table->string('day_name')->nullable();    // اسم اليوم

            // الحقول التي تحتوي على قوائم أسماء أو أرقام الموظفين بصيغة JSON
            $table->json('periodic_leaves')->nullable();       // إجازات دورية
            $table->json('annual_leaves')->nullable();         // إجازات سنوية
            $table->json('temporary_leaves')->nullable();      // إجازات زمنية
            $table->json('unpaid_leaves')->nullable();         // بدون راتب
            $table->json('absences')->nullable();              // غياب
            $table->json('long_leaves')->nullable();           // طويلة
            $table->json('sick_leaves')->nullable();           // مرضية
            $table->json('bereavement_leaves')->nullable();    // وفاة

            // الحقول الرقمية
            $table->integer('total_employees')->nullable();     // المجموع الكلي
            $table->integer('actual_attendance')->nullable();   // الحضور الفعلي
            $table->integer('paid_leaves_count')->nullable();   // إجازات براتب
            $table->integer('unpaid_leaves_count')->nullable(); // بدون راتب
            $table->integer('absences_count')->nullable();      // الغياب

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_statuses');
    }
}

