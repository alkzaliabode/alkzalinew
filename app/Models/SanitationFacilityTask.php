<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // تم إضافة هذا الاستيراد
use App\Models\User; // ✅ تم إعادة هذا الاستيراد

use App\Models\UnitGoal;
use App\Models\TaskImageReport;
use App\Models\ActualResult;
use App\Models\MonthlySanitationSummary;

class SanitationFacilityTask extends Model
{
    protected $fillable = [
        'unit_id',
        'date',
        'shift',
        'task_type',
        'facility_name',
        'details',
        'status',
        'notes',
        'related_goal_id',
        'progress',
        'result_value',
        'resources_used',
        'verification_status',
        'before_images',
        'after_images',
        'seats_count',
        'sinks_count',
        'mixers_count',
        'mirrors_count',
        'doors_count',
        'toilets_count',
        'working_hours'
        // لا تضع 'created_by' أو 'updated_by' هنا في الـ $fillable
        // لأننا نملأها يدوياً عبر أحداث الموديل لضمان الأمان
    ];

    protected $casts = [
        'resources_used' => 'array',
        'before_images' => 'array',
        'after_images' => 'array',
        'date' => 'date',
    ];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_task', 'sanitation_facility_task_id', 'employee_id')
            ->withPivot(['employee_rating', 'start_time', 'end_time', 'notes'])
            ->withTimestamps();
    }

    public function employeeTasks(): HasMany
    {
        return $this->hasMany(\App\Models\EmployeeTask::class, 'sanitation_facility_task_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function relatedGoal(): BelongsTo
    {
        return $this->belongsTo(UnitGoal::class, 'related_goal_id');
    }

    // علاقة مع المستخدم الذي أنشأ المهمة
    // الآن يمكن استخدام User::class بدلاً من \App\Models\User::class بفضل الاستيراد
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // علاقة مع المستخدم الذي عدّل المهمة آخر مرة
    // الآن يمكن استخدام User::class بدلاً من \App\Models\User::class بفضل الاستيراد
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function booted()
    {
        // التعيين التلقائي لـ unit_id = 2 قبل الإنشاء
        static::creating(function ($task) {
            $task->unit_id = $task->unit_id ?? 2;
            // تعيين created_by بمعرف المستخدم الحالي عند الإنشاء
            if (Auth::check()) {
                $task->created_by = Auth::id();
            }
        });

        // تعيين updated_by بمعرف المستخدم الحالي عند الحفظ (إنشاء أو تحديث)
        static::saving(function ($task) {
            if (Auth::check()) {
                $task->updated_by = Auth::id();
            }
        });

        static::created(function ($task) {
            self::recalculateMonthlySummary($task);
            self::handleTaskImageReport($task);
            if ($task->status === 'مكتمل' && $task->unit_id && $task->date) {
                ActualResult::recalculateForUnitAndDate($task->unit_id, $task->date);
            }
        });

        static::updated(function ($task) {
            self::recalculateMonthlySummary($task);
            self::handleTaskImageReport($task);
            if ($task->isDirty('status') && $task->status === 'مكتمل') {
                ActualResult::recalculateForUnitAndDate($task->unit_id, $task->date);
            }
        });

        static::deleted(function ($task) {
            self::recalculateMonthlySummary($task);
            self::cleanupTaskImages($task);
            if ($task->unit_id && $task->date) {
                ActualResult::recalculateForUnitAndDate($task->unit_id, $task->date);
            }
        });
    }

    protected static function recalculateMonthlySummary($task)
    {
        $facilityName = $task->facility_name;
        $taskType = $task->task_type;
        $date = Carbon::parse($task->date);
        $month = $date->format('Y-m');

        $summaryId = md5("{$month}-{$facilityName}-{$taskType}");

        $totals = self::whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->where('facility_name', $facilityName)
            ->where('task_type', $taskType)
            ->selectRaw('
                SUM(seats_count) as total_seats,
                SUM(mirrors_count) as total_mirrors,
                SUM(mixers_count) as total_mixers,
                SUM(doors_count) as total_doors,
                SUM(sinks_count) as total_sinks,
                SUM(toilets_count) as total_toilets,
                COUNT(*) as total_tasks
            ')
            ->first();

        MonthlySanitationSummary::updateOrCreate(
            [
                'id' => $summaryId,
            ],
            [
                'month' => $month,
                'facility_name' => $facilityName,
                'task_type' => $taskType,
                'total_seats' => $totals->total_seats ?? 0,
                'total_mirrors' => $totals->total_mirrors ?? 0,
                'total_mixers' => $totals->total_mixers ?? 0,
                'total_doors' => $totals->total_doors ?? 0,
                'total_sinks' => $totals->total_sinks ?? 0,
                'total_toilets' => $totals->total_toilets ?? 0,
                'total_tasks' => $totals->total_tasks ?? 0,
            ]
        );
    }

    protected static function handleTaskImageReport($task)
    {
        if (!empty($task->before_images) || !empty($task->after_images)) {
            $reportData = [
                'task_id' => $task->id,
                'unit_type' => 'health',
                'date' => $task->date,
                'location' => $task->facility_name,
                'task_type' => $task->task_type,
                'status' => $task->status,
                'notes' => $task->notes,
            ];

            if (!empty($task->before_images)) {
                $reportData['before_images'] = $task->before_images;
            }

            if (!empty($task->after_images)) {
                $reportData['after_images'] = $task->after_images;
            }

            TaskImageReport::updateOrCreate(
                [
                    'task_id' => $task->id,
                    'unit_type' => 'health',
                ],
                $reportData
            );
        }
    }

    protected static function cleanupTaskImages($task)
    {
        $report = TaskImageReport::where('task_id', $this->id)
                                    ->where('unit_type', 'health')
                                    ->first();

        if ($report) {
            $report->deleteRelatedImages();
            $report->delete();
        }
    }

    public function getBeforeImagesUrlsAttribute(): array
    {
        $report = TaskImageReport::where('task_id', $this->id)
                                    ->where('unit_type', 'health')
                                    ->first();
        return $report ? $report->getOriginalUrlsForTable($report->before_images) : [];
    }

    public function getAfterImagesUrlsAttribute(): array
    {
        $report = TaskImageReport::where('task_id', $this->id)
                                    ->where('unit_type', 'health')
                                    ->first();
        return $report ? $report->getOriginalUrlsForTable($report->after_images) : [];
    }
}
