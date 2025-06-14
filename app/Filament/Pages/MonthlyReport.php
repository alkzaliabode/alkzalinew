<?php

namespace App\Filament\Pages;

use App\Models\MonthlySanitationSummary;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class MonthlyReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.monthly-report';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'تقرير المنشآت الصحية الشهري';
    protected static ?string $title = '📊 تقرير شهري - المنشآت الصحية';
    protected static ?string $navigationGroup = 'التقارير والإحصائيات';

    protected function getTableQuery(): Builder
    {
        return MonthlySanitationSummary::query()->orderBy('month', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('month')->label('📅 الشهر')->sortable(),
            Tables\Columns\TextColumn::make('facility_name')->label('🏢 اسم المرفق')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('task_type')->label('🛠 نوع المهمة')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'إدامة' => 'info',
                    'صيانة' => 'warning',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('total_seats')->label('💺 المقاعد')->numeric(),
            Tables\Columns\TextColumn::make('total_mirrors')->label('🪞 المرايا')->numeric(),
            Tables\Columns\TextColumn::make('total_mixers')->label('🚰 الخلاطات')->numeric(),
            Tables\Columns\TextColumn::make('total_doors')->label('🚪 الأبواب')->numeric(),
            Tables\Columns\TextColumn::make('total_sinks')->label('🧼 المغاسل')->numeric(),
            Tables\Columns\TextColumn::make('total_toilets')->label('🚻 المراحيض')->numeric(),
            Tables\Columns\TextColumn::make('total_tasks')->label('📋 عدد المهام')->numeric(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('month')
    ->label('تصفية حسب الشهر')
    ->options(
        MonthlySanitationSummary::query()
            ->select('month') // month موجود فعليًا في الـ View
            ->groupBy('month') // هذا يحل المشكلة نهائيًا
            ->orderBy('month', 'desc')
            ->pluck('month', 'month')
            ->toArray()
                ),

            Tables\Filters\SelectFilter::make('facility_name')
                ->label('تصفية حسب اسم المرفق')
                ->options(
                    MonthlySanitationSummary::query()
                        ->select('facility_name')
                        ->distinct()
                        ->pluck('facility_name', 'facility_name')
                        ->toArray()
                ),

            Tables\Filters\SelectFilter::make('task_type')
                ->label('تصفية حسب نوع المهمة')
                ->options([
                    'إدامة' => 'إدامة',
                    'صيانة' => 'صيانة',
                ]),
        ];
    }

    public function getTableRecordKey($record): string
    {
        return md5(json_encode([
            $record->month,
            $record->facility_name,
            $record->task_type,
        ]));
    }

    protected function getTableHeaderActions(): array
    {
        return [
            FilamentExportHeaderAction::make('export')
                ->label('📤 تصدير التقرير')
                ->fileName('تقرير_المنشآت_الصحية_' . now()->format('Y-m-d')),
        ];
    }

    protected function getTableActions(): array
    {
        return [];
    }

    protected function getTableBulkActions(): array
    {
        return [];
    }

    protected function getTableEmptyStateActions(): array
    {
        return [];
    }
}