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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            
            // إضافة عمود survey_number هنا
            // يمكنك اختيار النوع المناسب (string, integer, etc.) وطول الحقل.
            // على سبيل المثال، string لتخزين أرقام الاستبيانات المعقدة
            // تم إزالة ->after('id') لجعلها متوافقة مع إصدارات MySQL/MariaDB الأقدم
            $table->string('survey_number')->nullable()->unique(); 

            // المعلومات العامة
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('age_group', ['under_18', '18_30', '30_45', '45_60', 'over_60'])->nullable();
            $table->enum('visit_count', ['first_time', '2_5_times', 'over_5_times'])->nullable();
            $table->enum('stay_duration', ['less_1h', '2_3h', '4_6h', 'over_6h'])->nullable();
            
            // تقييم نظافة المرافق
            $table->enum('toilet_cleanliness', ['excellent', 'very_good', 'good', 'acceptable', 'poor']);
            $table->enum('hygiene_supplies', ['always', 'often', 'rarely', 'never']);
            $table->enum('yard_cleanliness', ['clean', 'needs_improvement', 'dirty']);
            $table->enum('cleaning_teams', ['clearly', 'sometimes', 'rarely', 'not_noticed']);
            
            // تقييم أماكن الاستراحة
            $table->enum('hall_cleanliness', ['very_clean', 'clean', 'needs_improvement', 'dirty']);
            $table->enum('bedding_condition', ['excellent', 'needs_care', 'not_clean', 'not_available']);
            $table->enum('ventilation', ['excellent', 'needs_improvement', 'poor']);
            $table->enum('lighting', ['excellent', 'good', 'needs_improvement']);
            
            // تقييم خدمات سقاية المياه
            $table->enum('water_trams_distribution', ['everywhere', 'needs_more', 'not_enough']);
            $table->enum('water_trams_cleanliness', ['very_clean', 'clean', 'needs_improvement', 'dirty']);
            $table->enum('water_availability', ['always', 'often', 'rarely', 'not_enough']);
            
            // التقييم العام
            $table->enum('overall_satisfaction', ['very_satisfied', 'satisfied', 'acceptable', 'dissatisfied']);
            $table->text('problems_faced')->nullable();
            $table->text('suggestions')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
