<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\SanitationFacilityTask;
use App\Models\GeneralCleaningTask;

class ResourceReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'تقرير الموارد';
    protected static string $view = 'filament.pages.resource-report';
    protected static ?string $slug = 'resource-report';
    protected static ?string $navigationGroup = 'التقارير والإحصائيات';
    protected static ?int $navigationSort = 4; // Adjust the sort order as needed
    protected static ?string $recordTitleAttribute = 'date'; // Use date as the record title

    public array $resources = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && !auth()->user()->hasRole('filament_user');
    }

    public static function canAccess(): bool
    {
        return auth()->check() && !auth()->user()->hasRole('filament_user');
    }

    public function mount(): void
    {
        $resources = collect();

        SanitationFacilityTask::with('unit')->get()->each(function ($task) use (&$resources) {
            foreach ($task->resources_used ?? [] as $res) {
                $resources->push([
                    'date' => $task->date,
                    'unit' => $task->unit->name ?? '---',
                    'task_type' => $task->task_type,
                    'item' => $res['name'] ?? '-',
                    'quantity' => $res['quantity'] ?? 0,
                    'resource_unit' => $res['unit'] ?? '-',
                    'notes' => $res['notes'] ?? '',
                ]);
            }
        });

        GeneralCleaningTask::with('unit')->get()->each(function ($task) use (&$resources) {
            foreach ($task->resources_used ?? [] as $res) {
                $resources->push([
                    'date' => $task->date,
                    'unit' => $task->unit->name ?? '---',
                    'task_type' => $task->task_type,
                    'item' => $res['name'] ?? '-',
                    'quantity' => $res['quantity'] ?? 0,
                    'resource_unit' => $res['unit'] ?? '-',
                    'notes' => $res['notes'] ?? '',
                ]);
            }
        });

        $this->resources = $resources->sortByDesc('date')->values()->toArray();
    }
}
