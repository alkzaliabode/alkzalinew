<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskImageReportsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_image_reports', function (Blueprint $table) {
            $table->id();

            // مفتاح أجنبي يشير إلى المهمة (GeneralCleaningTask أو SanitationFacilityTask)
            $table->unsignedBigInteger('task_id')->index();

            // نوع الوحدة: تنظيف أو صحة
            $table->enum('unit_type', ['cleaning', 'health'])->index();

            // الحقول التي تخزن الصور بصيغة JSON
            $table->json('before_images')->nullable();
            $table->json('after_images')->nullable();

            // تاريخ المهمة
            $table->date('date')->index();

            // الموقع (مكان المهمة)
            $table->string('location')->index();

            // نوع المهمة: إدامة أو صيانة
            $table->string('task_type')->index();

            // حالة المهمة (مثلاً: مكتملة، معلقة...)
            $table->string('status')->nullable();

            // ملاحظات إضافية
            $table->text('notes')->nullable();

            $table->timestamps();

            // يمكنك إضافة مفتاح أجنبي إذا كانت هناك جداول GeneralCleaningTask و SanitationFacilityTask
            // ولكن هنا لأنه قد يكون نوعين مختلفين لنفس المفتاح، نحتفظ به كـ unsignedBigInteger فقط.
            // إذا تريد إضافة قيود مفتاح أجنبي يمكنك تعديلها حسب جداولك.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_image_reports');
    }
}
