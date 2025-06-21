<?php

namespace App\Filament\Resources\SanitationFacilityTaskResource\Pages;

use App\Filament\Resources\SanitationFacilityTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\MonthlySanitationSummary;
use App\Models\ActualResult;
use App\Models\SanitationFacilityTask;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Unit;

class EditSanitationFacilityTask extends EditRecord
{
    protected static string $resource = SanitationFacilityTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->form->model($this->record)->saveRelationships();

        if ($this->record->status === 'مكتمل') {
            $this->updateMonthlySummary();
            $this->updateDailyResult();
        }
    }

    protected function updateMonthlySummary(): void
    {
        $record = $this->record;

        $month = Carbon::parse($record->date)->format('Y-m');
        $facilityName = $record->facility_name;
        $taskType = $record->task_type;

        $summary = MonthlySanitationSummary::firstOrNew([
            'month' => $month,
            'facility_name' => $facilityName,
            'task_type' => $taskType,
        ]);

        if (!$summary->exists) {
            $summary->id = Str::uuid();
        }

        // زيادة المجاميع حسب الحقول الموجودة في المهمة
        $summary->total_seats += (int) $record->seats_count;
        $summary->total_mirrors += (int) $record->mirrors_count;
        $summary->total_mixers += (int) $record->mixers_count;
        $summary->total_doors += (int) $record->doors_count;
        $summary->total_sinks += (int) $record->sinks_count;
        $summary->total_toilets += (int) $record->toilets_count;
        $summary->total_tasks += 1;

        $summary->save();
    }

    protected function updateDailyResult(): void
    {
        $record = $this->record;

        $unitId = $record->unit_id;

        if (!$unitId) {
            $unit = Unit::where('name', 'وحدة المنشآت الصحية')->first();
            if (!$unit) {
                // عدم وجود الوحدة، لا يحدث تحديث
                return;
            }
            $unitId = $unit->id;
        }

        $date = $record->date;

        $completedTasksCount = SanitationFacilityTask::whereDate('date', $date)
            ->where('unit_id', $unitId)
            ->where('status', 'مكتمل')
            ->count();

        // إذا لديك علاقة employeeTasks وتريد حساب تقييمات الموظفين مثل السابق:
        $ratings = SanitationFacilityTask::whereDate('date', $date)
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', ['unit_id' => $this->record->unit_id]);
    }
}
