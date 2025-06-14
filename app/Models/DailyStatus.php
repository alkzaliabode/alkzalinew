<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStatus extends Model
{
    protected $fillable = [
        'date',
        'hijri_date',
        'day_name',
        'periodic_leaves',
        'annual_leaves',
        'temporary_leaves',
        'unpaid_leaves',
        'absences',
        'long_leaves',
        'sick_leaves',
        'bereavement_leaves',
        'total_employees',
        'actual_attendance',
        'paid_leaves_count',
        'unpaid_leaves_count',
        'absences_count',
        'shortage', // ✅ أضفه هنا
    ];

    protected $casts = [
        'periodic_leaves' => 'array',
        'annual_leaves' => 'array',
        'temporary_leaves' => 'array',
        'unpaid_leaves' => 'array',
        'absences' => 'array',
        'long_leaves' => 'array',
        'sick_leaves' => 'array',
        'bereavement_leaves' => 'array',
    ];
}
