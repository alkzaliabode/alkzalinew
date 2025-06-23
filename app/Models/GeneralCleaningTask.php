<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth; // ✅ مهم: تم إضافة استيراد Auth facade

// DB Facade و Storage Facade تم إزالتهما لأنها غير مستخدمة مباشرة في هذا الموديل
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\UnitGoal;
use App\Models\TaskImageReport;
use App\Models\ActualResult;
use App\Models\MonthlyGeneralCleaningSummary;

class GeneralCleaningTask extends Model
{
    protected $fillable = [
        'date', 'shift', 'task_type', 'location', 'quantity', 'status', 'notes',
        'responsible_persons', 'related_goal_id', 'progress', 'result_value',
        'resources_used', 'verification_status', 'before_images', 'after_images', 'unit_id',
        'working_hours', 'mats_count', 'pillows_count', 'fans_count', 'windows_count',
        'carpets_count', 'blankets_count', 'beds_count', 'beneficiaries_count',
        'filled_trams_count', 'carpets_laid_count', 'large_containers_count',
        'small_containers_count', 'maintenance_details',
        // لا تضع 'created_by' أو 'updated_by' هنا لأننا نملأها يدوياً عبر أحداث الموديل
    ];

    protected $casts = [
        'resources_used' => 'array',
        'before_images' => 'array',
        'after_images' => 'array',
        'date' => 'date',
    ];

    public function employeeTasks(): HasMany
    {
        return $this->hasMany(EmployeeTask::class, 'general_cleaning_task_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function relatedGoal(): BelongsTo
    {
        return $this->belongsTo(UnitGoal::class, 'related_goal_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function booted()
    {
        // عند إنشاء مهمة جديدة (قبل حفظها في قاعدة البيانات لأول مرة)
        static::creating(function ($task) {
            $task->unit_id = $task->unit_id ?? 1; // التعيين التلقائي لـ unit_id

            // ✅ تعيين created_by بمعرف المستخدم الحالي
            if (Auth::check()) {
                $task->created_by = Auth::id();
            }
        });

        // عند حفظ المهمة (سواء كانت إنشاء لأول مرة أو تحديث موجود)
        static::saving(function ($task) {
            // ✅ تعيين updated_by بمعرف المستخدم الحالي
            if (Auth::check()) {
                $task->updated_by = Auth::id();
            }
        });

        // الأحداث الأخرى التي كانت موجودة مسبقاً
        static::created(function ($task) {
            self::recalculateSummaries($task);
            self::handleTaskImageReport($task);
            if ($task->status === 'مكتمل' && $task->unit_id && $task->date) {
                ActualResult::recalculateForUnitAndDate($task->unit_id, $task->date);
            }
        });

        static::updated(function ($task) {
            self::recalculateSummaries($task);
            self::handleTaskImageReport($task);
            if ($task->isDirty('status') && $task->status === 'مكتمل') {
                ActualResult::recalculateForUnitAndDate($task->unit_id, $task->date);
            }
        });

        static::deleted(function ($task) {
            self::recalculateSummaries($task);
            self::cleanupTaskImages($task);
            if ($task->unit_id && $task->date) {
                ActualResult::recalculateForUnitAndDate($task->unit_id, $task->date);
            }
        });
    }

    protected static function recalculateSummaries($task)
    {
        if (!$task->unit_id) {
            return;
        }

        $unitId = $task->unit_id;
        $location = $task->location;
        $taskType = $task->task_type;
        $date = Carbon::parse($task->date);
        $month = $date->format('Y-m');

        $summaryId = md5("{$month}-{$location}-{$taskType}");

        $totals = self::whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->where('unit_id', $unitId)
            ->where('location', $location)
            ->where('task_type', $taskType)
            ->selectRaw('
                SUM(mats_count) as total_mats,
                SUM(pillows_count) as total_pillows,
                SUM(fans_count) as total_fans,
                SUM(windows_count) as total_windows,
                SUM(carpets_count) as total_carpets,
                SUM(blankets_count) as total_blankets,
                SUM(beds_count) as total_beds,
                SUM(beneficiaries_count) as total_beneficiaries,
                SUM(filled_trams_count) as total_trams,
                SUM(carpets_laid_count) as total_laid_carpets,
                SUM(large_containers_count) as total_large_containers,
                SUM(small_containers_count) as total_small_containers,
                COUNT(*) as total_tasks_count_for_summary
            ')
            ->first();

        MonthlyGeneralCleaningSummary::updateOrCreate(
            [
                'id' => $summaryId,
                'month' => $month,
                'location' => $location,
                'task_type' => $taskType,
            ],
            [
                'total_mats' => $totals->total_mats ?? 0,
                'total_pillows' => $totals->total_pillows ?? 0,
                'total_fans' => $totals->total_fans ?? 0,
                'total_windows' => $totals->total_windows ?? 0,
                'total_carpets' => $totals->total_carpets ?? 0,
                'total_blankets' => $totals->total_blankets ?? 0,
                'total_beds' => $totals->total_beds ?? 0,
                'total_beneficiaries' => $totals->total_beneficiaries ?? 0,
                'total_trams' => $totals->total_trams ?? 0,
                'total_laid_carpets' => $totals->total_laid_carpets ?? 0,
                'total_large_containers' => $totals->total_large_containers ?? 0,
                'total_small_containers' => $totals->total_small_containers ?? 0,
                'total_tasks' => $totals->total_tasks_count_for_summary ?? 0,
            ]
        );
    }

    protected static function handleTaskImageReport($task)
    {
        if (!empty($task->before_images) || !empty($task->after_images)) {
            $reportData = [
                'task_id' => $task->id,
                'unit_type' => 'cleaning',
                'date' => $task->date,
                'location' => $task->location,
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

            \App\Models\TaskImageReport::updateOrCreate(
                [
                    'task_id' => $task->id,
                    'unit_type' => 'cleaning',
                ],
                $reportData
            );
        }
    }

    public function getBeforeImagesUrlsAttribute(): array
    {
        $report = TaskImageReport::where('task_id', $this->id)
                                    ->where('unit_type', 'cleaning')
                                    ->first();
        return $report ? $report->getOriginalUrlsForTable($report->before_images) : [];
    }

    public function getAfterImagesUrlsAttribute(): array
    {
        $report = TaskImageReport::where('task_id', $this->id)
                                    ->where('unit_type', 'cleaning')
                                    ->first();
        return $report ? $report->getOriginalUrlsForTable($report->after_images) : [];
    }

    protected static function cleanupTaskImages($task)
    {
        $report = TaskImageReport::where('task_id', $task->id)
                                    ->where('unit_type', 'cleaning')
                                    ->first();

        if ($report) {
            $report->deleteRelatedImages();
            $report->delete();
        }
    }
}
