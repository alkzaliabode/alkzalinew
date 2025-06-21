<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB; // DB Facade لا يزال مفيدًا للاستعلامات المعقدة
use Illuminate\Support\Facades\Storage; // لا يزال مفيدًا في حال احتجنا للتخزين المباشر

use App\Models\UnitGoal;
use App\Models\TaskImageReport;
use App\Models\ActualResult;
use App\Models\MonthlyGeneralCleaningSummary; // ✅ تم إضافة هذا الاستيراد للموديل الجديد

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

   protected static function booted()
{
    static::creating(function ($task) {
        $task->unit_id = $task->unit_id ?? 1; // 👈 التعيين التلقائي قبل الإنشاء
    });

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

        // ✅ Generate a unique ID for the primary key of the summary table
        // It should be a combination of month, location, and task type
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

        // ✅ تم التعديل هنا: استخدام موديل MonthlyGeneralCleaningSummary بدلاً من DB::table
        MonthlyGeneralCleaningSummary::updateOrCreate(
            [
                'id' => $summaryId, // ✅ Pass the generated ID here
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
                // 'updated_at' و 'created_at' سيتم التعامل معهما تلقائياً
                // بعد إزالة public $timestamps = false; من MonthlyGeneralCleaningSummary model
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

    protected static function cleanupTaskImages($task)
    {
        // ✅ استخدام موديل TaskImageReport لتحديد الصور المرتبطة وحذفها
        $report = TaskImageReport::where('task_id', $task->id)
                                 ->where('unit_type', 'cleaning')
                                 ->first();

        if ($report) {
            $report->deleteRelatedImages(); // استدعاء الدالة من TaskImageReport
            $report->delete(); // حذف سجل التقرير من جدول TaskImageReport
        }
    }

    // Accessors for image URLs
    // ✅ تم التعديل هنا: استخدام TaskImageReport لجلب URLs للصور
    public function getBeforeImagesUrlsAttribute(): array
    {
        $report = TaskImageReport::where('task_id', $this->id)
                                 ->where('unit_type', 'cleaning')
                                 ->first();
        return $report ? $report->getOriginalUrlsForTable($report->before_images) : [];
    }

    // ✅ تم التعديل هنا: استخدام TaskImageReport لجلب URLs للصور
    public function getAfterImagesUrlsAttribute(): array
    {
        $report = TaskImageReport::where('task_id', $this->id)
                                 ->where('unit_type', 'cleaning')
                                 ->first();
        return $report ? $report->getOriginalUrlsForTable($report->after_images) : [];
    }

    // ✅ تم إزالة دالة convertToImageUrls() لأنها لم تعد مستخدمة
    // حيث أصبحت getBeforeImagesUrlsAttribute و getAfterImagesUrlsAttribute
    // تعتمدان على TaskImageReport::getOriginalUrlsForTable()
}
