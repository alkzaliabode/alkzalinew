<?php

namespace App\Filament\Resources\GeneralCleaningTaskResource\Pages;

use App\Filament\Resources\GeneralCleaningTaskResource;
use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\MonthlyGeneralCleaningSummary;
use App\Models\ActualResult;
use App\Models\GeneralCleaningTask;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Unit;

class EditGeneralCleaningTask extends EditRecord
{
    protected static string $resource = GeneralCleaningTaskResource::class;

    // أزرار الهيدر (رأس الصفحة)
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }



    // تنفيذ إجراءات بعد الحفظ
    protected function afterSave(): void
    {
        $this->form->model($this->record)->saveRelationships();

        if ($this->record->status === 'مكتمل') {
            $this->updateMonthlySummary();
            $this->updateDailyResult();
        }
    }

    // تحديث ملخص النظافة الشهري
    protected function updateMonthlySummary(): void
    {
        $record = $this->record;

        $month = Carbon::parse($record->date)->format('Y-m');
        $location = $record->location;
        $taskType = $record->task_type;

        $summary = MonthlyGeneralCleaningSummary::firstOrNew([
            'month' => $month,
            'location' => $location,
            'task_type' => $taskType,
        ]);

        if (!$summary->exists) {
            $summary->id = Str::uuid();
        }

        $summary->total_mats += (int) $record->mats_count;
        $summary->total_pillows += (int) $record->pillows_count;
        $summary->total_fans += (int) $record->fans_count;
        $summary->total_windows += (int) $record->windows_count;
        $summary->total_carpets += (int) $record->carpets_count;
        $summary->total_blankets += (int) $record->blankets_count;
        $summary->total_beds += (int) $record->beds_count;
        $summary->total_beneficiaries += (int) $record->beneficiaries_count;
        $summary->total_trams += (int) $record->filled_trams_count;
        $summary->total_laid_carpets += (int) $record->carpets_laid_count;
        $summary->total_large_containers += (int) $record->large_containers_count;
        $summary->total_small_containers += (int) $record->small_containers_count;

        $summary->save();
    }

    // تحديث النتائج اليومية
   protected function updateDailyResult(): void
{
    $record = $this->record;

    $unitId = $record->unit_id;

    if (!$unitId) {
        $unit = Unit::where('name', 'وحدة النظافة العامة')->first();
        if (!$unit) {
            // لم يتم العثور على وحدة النظافة العامة، لا يتم التحديث لتجنب الخطأ
            return;
        }
        $unitId = $unit->id;
    }

    $date = $record->date;

    $completedTasksCount = GeneralCleaningTask::whereDate('date', $date)
        ->where('unit_id', $unitId)
        ->where('status', 'مكتمل')
        ->count();

    $ratings = GeneralCleaningTask::whereDate('date', $date)
        ->where('unit_id', $unitId)
        ->with('employeeTasks')
        ->get()
        ->flatMap(fn ($task) => $task->employeeTasks->pluck('employee_rating'))
        ->filter();

    $averageRating = $ratings->count() ? round($ratings->avg()) : null;

    ActualResult::updateOrCreate(
        ['date' => $date, 'unit_id' => $unitId],
        [
            'completed_tasks' => $completedTasksCount,
            'quality_rating' => $averageRating,
            'efficiency_score' => null,
        ]
    );

  }

    // تحديد رابط إعادة التوجيه بعد الحفظ
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', ['unit_id' => $this->record->unit_id]);
    }
}
