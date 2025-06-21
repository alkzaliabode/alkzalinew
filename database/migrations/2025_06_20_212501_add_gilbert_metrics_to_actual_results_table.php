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
        Schema::table('actual_results', function (Blueprint $table) {
            // إضافة حقول مثلث الأداء Gilbert
            $table->decimal('working_hours', 8, 2)->nullable()->comment('ساعات العمل');
            $table->decimal('effectiveness', 8, 2)->nullable()->comment('الفعالية %');
            $table->decimal('efficiency', 8, 2)->nullable()->comment('الكفاءة %');
            $table->decimal('relevance', 8, 2)->nullable()->comment('الملاءمة %');
            $table->decimal('overall_performance_score', 8, 2)->nullable()->comment('نقاط الأداء الإجمالية %');
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            // إضافة فهارس للبحث السريع
            $table->index(['effectiveness']);
            $table->index(['efficiency']);
            $table->index(['relevance']);
            $table->index(['overall_performance_score']);
            $table->index(['unit_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actual_results', function (Blueprint $table) {
            // حذف الفهارس أولاً
            $table->dropIndex(['effectiveness']);
            $table->dropIndex(['efficiency']);
            $table->dropIndex(['relevance']);
            $table->dropIndex(['overall_performance_score']);
            $table->dropIndex(['unit_id', 'date']);
            
            // حذف الحقول
            $table->dropColumn([
                'working_hours',
                'effectiveness',
                'efficiency',
                'relevance',
                'overall_performance_score',
                'notes',
            ]);
        });
    }
};