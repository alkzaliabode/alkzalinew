<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_trackings', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->unsignedBigInteger('unit_id')->comment('الوحدة المرتبطة'); // معرف الوحدة
            $table->integer('working_hours')->default(0)->comment('إجمالي ساعات العمل'); // ✅ هذا هو working_hours
            $table->integer('cleaning_materials')->default(0)->comment('كمية مواد التنظيف المستهلكة (لتر)');
            $table->integer('water_consumption')->default(0)->comment('استهلاك المياه (لتر)');
            $table->integer('equipment_usage')->default(0)->comment('عدد المعدات المستخدمة');
            $table->text('notes')->nullable()->comment('ملاحظات إضافية');
            $table->timestamps();

            // إضافة قيد فريد لضمان عدم وجود أكثر من سجل تتبع موارد لـ unit_id معين في تاريخ معين
            $table->unique(['date', 'unit_id']);
            
            // تعريف المفتاح الخارجي لـ unit_id
            // هنا كان لديك foreign('unit_id')->references('id')->on('unit_goals')
            // ولكن unit_id في هذا الجدول هو معرف الوحدة نفسها، وليس معرف الهدف
            // لذا يجب أن يشير إلى جدول الوحدات 'units'
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_trackings');
    }
};
