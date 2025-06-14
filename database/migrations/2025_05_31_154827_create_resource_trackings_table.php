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
            $table->unsignedBigInteger('unit_id')->comment('الوحدة المرتبطة');
            $table->integer('working_hours')->default(0)->comment('إجمالي ساعات العمل');
            $table->integer('cleaning_materials')->default(0)->comment('كمية مواد التنظيف المستهلكة (لتر)');
            $table->integer('water_consumption')->default(0)->comment('استهلاك المياه (لتر)');
            $table->integer('equipment_usage')->default(0)->comment('عدد المعدات المستخدمة');
            $table->text('notes')->nullable()->comment('ملاحظات إضافية');
            $table->timestamps();

            $table->unique(['date', 'unit_id']);
            $table->foreign('unit_id')->references('id')->on('unit_goals')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_trackings');
    }
};
