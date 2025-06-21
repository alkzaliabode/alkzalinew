<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use App\Models\GeneralCleaningTask;
use App\Models\SanitationFacilityTask;
use App\Models\UnitGoal;
use App\Models\DepartmentGoal;
use App\Models\ResourceTracking;

class ActualResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'completed_tasks',
        'quality_rating',
        'efficiency_score',
        'unit_id',
        'department_goal_id',
        'unit_goal_id',
        'working_hours',
        'effectiveness',
        'efficiency',
        'relevance',
        'overall_performance_score',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'effectiveness' => 'float',
        'efficiency' => 'float',
        'relevance' => 'float',
        'overall_performance_score' => 'float',
        'quality_rating' => 'float',
    ];

    /**
     * العلاقة بين النتيجة والوحدة.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * العلاقة بين النتيجة وهدف الوحدة.
     */
    public function unitGoal(): BelongsTo
    {
        return $this->belongsTo(UnitGoal::class);
    }

    /**
     * العلاقة بين النتيجة وهدف القسم.
     */
    public function departmentGoal(): BelongsTo
    {
        return $this->belongsTo(DepartmentGoal::class);
    }

    /**
     * حساب معدل الإنجاز بناءً على الهدف المستهدف لهذه الوحدة والتاريخ.
     */
    public function getCompletionRateAttribute(): float
    {
        $targetTasks = $this->unitGoal?->target_tasks ?? 0;

        if ($targetTasks == 0) {
            return 0.0;
        }

        return round(($this->completed_tasks / $targetTasks) * 100, 2);
    }

    /**
     * تقوم تلقائياً بحساب إجمالي المهام المكتملة من الموديلات المرتبطة
     * وتحدّث/تنشئ سجل ActualResult، بما في ذلك مقاييس غيلبرت.
     *
     * @param int $unitId معرف الوحدة.
     * @param string|\Carbon\Carbon $date التاريخ.
     * @param int|null $relatedGoalId معرف الهدف المرتبط من المهمة (لتحديد UnitGoal المحدد).
     * @return void
     */
    public static function recalculateForUnitAndDate($unitId, $date, $relatedGoalId = null): void
    {
        if (!$unitId || !$date) {
            // يمكن طباعة رسالة خطأ هنا للمساعدة في التصحيح
            // echo "Unit ID or date is missing for recalculation.\n";
            return;
        }

        $parsedDate = Carbon::parse($date)->format('Y-m-d');

        // جلب المهام المكتملة من GeneralCleaningTask
        $completedGeneral = GeneralCleaningTask::where('unit_id', $unitId)
            ->whereDate('date', $parsedDate)
            ->where('status', 'مكتمل')
            ->count();

        // جلب المهام المكتملة من SanitationFacilityTask
        $completedSanitation = SanitationFacilityTask::where('unit_id', $unitId)
            ->whereDate('date', $parsedDate)
            ->where('status', 'مكتمل')
            ->count();

        $totalCompleted = $completedGeneral + $completedSanitation;

        // ✅ تحديث منطق جلب هدف الوحدة المرتبط (UnitGoal)
        $goal = null;
        if ($relatedGoalId) {
            // إذا تم تمرير relatedGoalId، حاول العثور على هذا الهدف المحدد
            $goal = UnitGoal::find($relatedGoalId);
        }

        // إذا لم يتم العثور على هدف محدد عبر relatedGoalId (أو لم يتم تمريره)،
        // حاول العثور على هدف للوحدة والتاريخ، أو أي هدف للوحدة كخيار أخير.
        if (!$goal) {
            // محاولة العثور على هدف لليوم المحدد (إذا كانت الأهداف يومية)
            $goal = UnitGoal::where('unit_id', $unitId)
                ->whereDate('date', $parsedDate) // افترض أن UnitGoal لديه عمود 'date'
                ->first();

            if (!$goal) {
                // إذا لم يتم العثور على هدف يومي، حاول جلب أي هدف للوحدة
                // (قد تحتاج إلى منطق أكثر تحديداً هنا، مثل أحدث هدف أو الهدف النشط حالياً)
                $goal = UnitGoal::where('unit_id', $unitId)->first();
            }
        }


        // تحديد المهام المستهدفة للحسابات
        $targetTasksForCalculation = $goal?->target_tasks ?? 0;
        if ($targetTasksForCalculation === 0) {
            $targetTasksForCalculation = 1; // تجنب القسمة على صفر
        }

        // جلب سجل ResourceTracking المرتبط لساعات العمل
        $resourceTracking = ResourceTracking::where('unit_id', $unitId)
            ->whereDate('date', $parsedDate)
            ->first();

        // تحديد ساعات العمل للحسابات (افتراض 8 إذا لم يتم العثور عليها)
        $workingHoursForCalculation = $resourceTracking ? (float) $resourceTracking->working_hours : 8;

        // --- حسابات مقاييس أداء غيلبرت ---

        // الفعالية (Effectiveness): (النتائج الفعلية / الأهداف) * 100
        $effectiveness = ($targetTasksForCalculation > 0) ? ($totalCompleted / $targetTasksForCalculation) * 100 : 0;

        // الكفاءة (Efficiency): (النتائج الفعلية / الموارد) * 100
        $efficiency = ($workingHoursForCalculation > 0) ? ($totalCompleted / $workingHoursForCalculation) * 100 : 0;

        // الملاءمة (Relevance): (الموارد / الأهداف) * 100
        $relevance = ($targetTasksForCalculation > 0) ? ($workingHoursForCalculation / $targetTasksForCalculation) * 100 : 0;

        // الأداء الإجمالي (Overall Performance Score): متوسط الثلاثة
        $overallScore = 0;
        $validMetricsCount = 0;
        if ($effectiveness > 0) { $overallScore += $effectiveness; $validMetricsCount++; }
        if ($efficiency > 0) { $overallScore += $efficiency; $validMetricsCount++; }
        if ($relevance > 0) { $overallScore += $relevance; $validMetricsCount++; }
        $overallScore = ($validMetricsCount > 0) ? $overallScore / $validMetricsCount : 0;

        // جلب سجل ActualResult الحالي إذا كان موجوداً للحفاظ على الحقول غير المحسوبة
        $existingActualResult = self::where('unit_id', $unitId)
            ->where('date', $parsedDate)
            ->first();

        // ✅ الشرط الجديد: فقط نحدث أو ننشئ إذا كان هناك هدف (UnitGoal) مرتبط **وصالح**
        // هذا يضمن أن unit_goal_id سيكون له قيمة صالحة قبل محاولة الإدخال
        if ($goal && $goal->id) { // تأكد من أن الهدف موجود ولديه ID
            self::updateOrCreate(
                [
                    'unit_id' => $unitId,
                    'date' => $parsedDate,
                    // ✅ بما أن unique constraint هو ['date', 'unit_goal_id']، يجب تضمين unit_goal_id في شروط البحث
                    // وإلا سيقوم بإنشاء سجل جديد إذا كان unit_goal_id مختلفًا
                    'unit_goal_id' => $goal->id,
                ],
                [
                    'completed_tasks' => $totalCompleted,
                    'quality_rating' => $existingActualResult->quality_rating ?? null,
                    'efficiency_score' => $existingActualResult->efficiency_score ?? null,
                    'department_goal_id' => $goal->department_goal_id,
                    'unit_goal_id' => $goal->id, // هذا سيضمن دائماً توفير القيمة
                    'working_hours' => round($workingHoursForCalculation, 2),
                    'effectiveness' => round($effectiveness, 2),
                    'efficiency' => round($efficiency, 2),
                    'relevance' => round($relevance, 2),
                    'overall_performance_score' => round($overallScore, 2),
                    'notes' => $existingActualResult->notes ?? null,
                ]
            );
        } else {
            // يمكن طباعة رسالة هنا إذا لم يتم العثور على هدف
            // echo "No valid UnitGoal found for unit ID: {$unitId} on date: {$parsedDate}. ActualResult not created.\n";
        }
    }
}
