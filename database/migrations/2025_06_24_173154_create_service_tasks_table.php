<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل المهاجرين (Run the migrations).
     */
    public function up(): void
    {
        Schema::create('service_tasks', function (Blueprint $table) {
            $table->id(); // عمود المعرف الرئيسي التزايدي
            $table->string('title'); // عنوان المهمة
            $table->text('description')->nullable(); // وصف المهمة (يمكن أن يكون فارغاً)
            $table->string('unit')->default('cleaning'); // وحدة المهمة: 'cleaning' أو 'health_facilities'
            $table->string('status')->default('pending'); // حالة المهمة: 'pending', 'in_progress', 'completed', 'cancelled'
            $table->integer('order_column')->nullable(); // عمود لترتيب المهام (مهم لـ spatie/laravel-sortable)
            $table->date('due_date')->nullable(); // تاريخ الاستحقاق للمهمة (يمكن أن يكون فارغاً)
            // عمود المفتاح الأجنبي الذي يشير إلى جدول 'users'
            // تأكد من وجود جدول 'users' قبل تشغيل هذا المهاجر
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->onDelete('set null');
            $table->string('priority')->default('medium'); // أولوية المهمة: 'low', 'medium', 'high'
            $table->timestamps(); // أعمدة created_at و updated_at
        });
    }

    /**
     * التراجع عن المهاجرين (Reverse the migrations).
     */
    public function down(): void
    {
        Schema::dropIfExists('service_tasks'); // حذف الجدول إذا كان موجوداً
    }
};

