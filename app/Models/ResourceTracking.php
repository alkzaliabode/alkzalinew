<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceTracking extends Model
{
    protected $fillable = [
        'date',
        'unit_id',
        'working_hours',
        'cleaning_materials',
        'water_consumption',
        'equipment_usage',
        'notes'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    // علاقة بالوحدة (Unit)
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class); // تأكد من وجود موديل Unit
    }

    // ✅ إضافة Accessor لـ 'total_working_hours'
    // هذا سيعيد قيمة 'working_hours' ولكن يمكن الوصول إليه كـ $resourceTracking->total_working_hours
    public function getTotalWorkingHoursAttribute(): int
    {
        return $this->working_hours;
    }

    // حساب الكفاءة الإجمالية (يعتمد على working_hours)
    public function getEfficiencyAttribute(): float
    {
        // تأكد من أن GeneralCleaningTask موجودة وأن بها علاقة مع هذا الموديل أو يمكن ربطها
        // هذا المثال يفترض أن working_hours هو فعلاً إجمالي الساعات ذات الصلة
        $completedTasks = GeneralCleaningTask::whereDate('date', $this->date)
                               ->where('unit_id', $this->unit_id)
                               ->where('status', 'مكتمل')
                               ->count();
        
        return $this->working_hours > 0 
            ? round($completedTasks / $this->working_hours, 2)
            : 0;
    }
}
