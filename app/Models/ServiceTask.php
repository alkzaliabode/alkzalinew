<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'unit',
        'status',
        'order_column',
        'due_date',
        'assigned_to',
        'priority',
    ];

    protected $casts = [
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'unit' => 'النظافة العامة',
        'status' => 'pending',
        'priority' => 'medium',
    ];

    public const STATUSES = [
        'pending' => 'معلقة',
        'in_progress' => 'قيد التنفيذ',
        'completed' => 'مكتملة',
        'rejected' => 'مرفوضة',
    ];

    public const UNITS = [
        'GeneralCleaning' => 'النظافة العامة',
        'SanitationFacility' => 'المنشآت الصحية',
    ];

    public const PRIORITIES = [
        'low' => 'منخفضة',
        'medium' => 'متوسطة',
        'high' => 'عالية',
    ];

    /**
     * علاقة المسؤول عن تنفيذ المهمة
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    /**
     * اسم المسؤول - لاستخدامه في Livewire و Kanban
     */
    public function getAssigneeNameAttribute(): ?string
    {
        return $this->assignedTo?->name;
    }

    /**
     * Accessor: لون الأولوية
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }

    /**
     * Accessor: أيقونة الوحدة
     */
    public function getUnitIconAttribute(): string
    {
        return match ($this->unit) {
            'النظافة العامة' => 'heroicon-o-broom',
            'المنشآت الصحية' => 'heroicon-o-building-office',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    /**
     * Accessor: هل المهمة متأخرة؟
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /** Scopes */

    public function scopeByUnit($query, string $unit)
    {
        return $query->where('unit', $unit);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->where('status', '!=', 'completed');
    }

    protected static function boot(): void
    {
        parent::boot();
        // تخصيص order_column يمكن تفعيله هنا إن أردت
    }
}
