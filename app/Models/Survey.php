<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = [
        // معلومات عامة
        'gender',
        'survey_number',
        'age_group',
        'visit_count',
        'stay_duration',

        // تقييم نظافة المرافق
        'toilet_cleanliness',
        'hygiene_supplies',
        'yard_cleanliness',
        'cleaning_teams',

        // تقييم أماكن الاستراحة
        'hall_cleanliness',
        'bedding_condition',
        'ventilation',
        'lighting',

        // تقييم خدمات سقاية المياه
        'water_trams_distribution',
        'water_trams_cleanliness',
        'water_availability',

        // التقييم العام
        'overall_satisfaction',
        'problems_faced',
        'suggestions',
    ];

    protected $casts = [
        'problems_faced' => 'string',
        'suggestions' => 'string',
    ];

    protected static function booted()
    {
        static::creating(function ($survey) {
            // توليد رقم الاستبيان تلقائياً إذا لم يُعطَ
            if (empty($survey->survey_number)) {
                $lastId = static::max('id') + 1;
                $survey->survey_number = 'SURV-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // يمكنك إضافة سمات أخرى أو علاقات إن لزم لاحقاً
}
