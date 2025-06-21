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
        'eid_leaves',
        'guard_rest',
        'total_employees',
        'actual_attendance',
        'paid_leaves_count',
        'unpaid_leaves_count',
        'absences_count',
        'shortage',
        // --- إضافة الحقول الجديدة هنا ---
        'organizer_employee_id',   // لـ ID الموظف المنظم
        'organizer_employee_name', // لاسم الموظف المنظم
    ];

    protected $casts = [
        'date' => 'date', // غالباً ما يكون مفيداً تحويل التاريخ إلى كائن Carbon
        'periodic_leaves' => 'array',
        'annual_leaves' => 'array',
        'temporary_leaves' => 'array',
        'unpaid_leaves' => 'array',
        'absences' => 'array',
        'long_leaves' => 'array',
        'sick_leaves' => 'array',
        'bereavement_leaves' => 'array',
        'eid_leaves' => 'array',
        'guard_rest' => 'array',
        // --- إضافة التحويلات للحقول الجديدة هنا ---
        'organizer_employee_id' => 'integer',   // تحويل إلى عدد صحيح
        'organizer_employee_name' => 'string',  // تحويل إلى نص
    ];
}