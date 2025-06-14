<?php

namespace App\Filament\Pages;

use App\Models\MonthlyGeneralCleaningSummary;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class MonthlyCleaningReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.monthly-cleaning-report';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'تقرير النظافة العامة الشهري';
    protected static ?string $title = '📊 تقرير شهري - النظافة العامة';
    protected static ?string $navigationGroup = 'التقارير والإحصائيات';

    protected function getTableQuery(): Builder
    {
        return MonthlyGeneralCleaningSummary::query()
            ->orderBy('month', 'desc')
            ->orderBy('location', 'asc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('month')->label('📅 الشهر')->sortable(),
            Tables\Columns\TextColumn::make('location')->label('📍 الموقع')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('task_type')->label('🛠 نوع المهمة')->sortable(),

            Tables\Columns\TextColumn::make('total_mats')->label('🧺 المنادر')->numeric(),
            Tables\Columns\TextColumn::make('total_pillows')->label('🛏 الوسائد')->numeric(),
            Tables\Columns\TextColumn::make('total_fans')->label('🌀 المراوح')->numeric(),
            Tables\Columns\TextColumn::make('total_windows')->label('🪟 النوافذ')->numeric(),
            Tables\Columns\TextColumn::make('total_carpets')->label('🪞 السجاد')->numeric(),
            Tables\Columns\TextColumn::make('total_blankets')->label('🧣 البطانيات')->numeric(),
            Tables\Columns\TextColumn::make('total_beds')->label('🛏 الأسرة')->numeric(),
            Tables\Columns\TextColumn::make('total_beneficiaries')->label('👥 المستفيدون')->numeric(),
            Tables\Columns\TextColumn::make('total_trams')->label('🚰 الترامز')->numeric(),
            Tables\Columns\TextColumn::make('total_laid_carpets')->label('🧼 السجاد المفروش')->numeric(),
            Tables\Columns\TextColumn::make('total_large_containers')->label('🗑 الحاويات الكبيرة')->numeric(),
            Tables\Columns\TextColumn::make('total_small_containers')->label('🗑 الحاويات الصغيرة')->numeric(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('month')
                ->label('تصفية حسب الشهر')
                ->options(
                    MonthlyGeneralCleaningSummary::query()
                        ->select('month')
                        ->distinct()
                        ->orderBy('month', 'desc')
                        ->pluck('month', 'month')
                        ->toArray()
                ),

            Tables\Filters\SelectFilter::make('location')
                ->label('تصفية حسب الموقع')
                ->options(
                    MonthlyGeneralCleaningSummary::query()
                        ->select('location')
                        ->distinct()
                        ->pluck('location', 'location')
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
        return $record->id ?? md5(json_encode([
            $record->month,
            $record->location,
            $record->task_type,
        ]));
    }

    protected function getTableHeaderActions(): array
    {
        return [
            FilamentExportHeaderAction::make('export-cleaning-report')
                ->label('📤 تصدير التقرير')
                ->fileName('تقرير_النظافة_العامة_' . now()->format('Y-m-d')),
        ];
    }

    protected function getTableActions(): array
    {
        return []; // لا حاجة لأزرار تعديل/عرض
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