<?php

namespace App\Filament\Pages;

use App\Models\TaskImageReport;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions;
use Carbon\Carbon;
use Filament\Forms\Components;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;


class ProfessionalImageReports extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.professional-image-reports';

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'التقارير المصورة الاحترافية';
    protected static ?string $title = '📷 التقارير المصورة الاحترافية';
    protected static ?string $navigationGroup = 'التقارير والإحصائيات';



    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('report_title')
                ->label('عنوان التقرير')
                ->searchable()
                ->sortable(),

            TextColumn::make('date')
                ->label('التاريخ')
                ->date('Y-m-d')
                ->sortable(),

            TextColumn::make('unit_type')
                ->label('الوحدة')
                ->formatStateUsing(fn (string $state): string => $state === 'cleaning' ? 'النظافة العامة' : 'المنشآت الصحية')
                ->badge()
                ->color(fn (string $state): string => $state === 'cleaning' ? 'success' : 'primary'),

            TextColumn::make('location')
                ->label('الموقع')
                ->searchable()
                ->sortable(),

            TextColumn::make('task_type')
                ->label('نوع المهمة')
                ->badge()
                ->color(fn (string $state): string => $state === 'إدامة' ? 'info' : 'warning'),

            ViewColumn::make('before_images')
                ->label('قبل التنفيذ')
                ->view('filament.tables.columns.image-gallery')
                ->viewData(['type' => 'before']),

            ViewColumn::make('after_images')
                ->label('بعد التنفيذ')
                ->view('filament.tables.columns.image-gallery')
                ->viewData(['type' => 'after']),

            TextColumn::make('status')
                ->label('الحالة')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'مكتمل' => 'success',
                    'قيد التنفيذ' => 'warning',
                    'ملغى' => 'danger',
                    default => 'gray',
                }),

            TextColumn::make('images_count')
                ->label('عدد الصور')
                ->formatStateUsing(function ($state, $record) {
                    return "قبل: {$record->before_images_count} | بعد: {$record->after_images_count}";
                })
                ->badge()
                ->color('info'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\ViewAction::make()
                ->label('عرض التفاصيل')
                ->icon('heroicon-o-eye')
                ->modalContent(fn (TaskImageReport $record) => view('filament.pages.image-report', [
                    'record' => $record,
                    'unitName' => $record->unit_type === 'cleaning' ? 'النظافة العامة' : 'المنشآت الصحية'
                ]))
                ->modalWidth('7xl'),

            Tables\Actions\Action::make('print_single_report')
                ->label('طباعة التقرير')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn (TaskImageReport $record): string => route('print.image.report', ['record' => $record->id]))
                ->openUrlInNewTab(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->filters([
                Tables\Filters\SelectFilter::make('unit_type')
                    ->label('الوحدة')
                    ->options([
                        'cleaning' => 'النظافة العامة',
                        'health' => 'المنشآت الصحية',
                    ]),

                Tables\Filters\SelectFilter::make('date')
                    ->label('الشهر')
                    ->options(
                        TaskImageReport::query()
                            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month')
                            ->distinct()
                            ->orderBy('month', 'desc')
                            ->pluck('month', 'month')
                            ->toArray()
                    )
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value']) && $data['value']) {
                            $month = Carbon::parse($data['value'])->format('Y-m');
                            return $query->whereRaw('DATE_FORMAT(date, "%Y-%m") = ?', [$month]);
                        }
                        return $query;
                    }),

                Tables\Filters\SelectFilter::make('location')
                    ->label('الموقع')
                    ->searchable()
                    ->options(
                        TaskImageReport::query()
                            ->select('location')
                            ->distinct()
                            ->pluck('location', 'location')
                            ->toArray()
                    ),

                Tables\Filters\SelectFilter::make('task_type')
                    ->label('نوع المهمة')
                    ->options([
                        'إدامة' => 'إدامة',
                        'صيانة' => 'صيانة',
                    ]),
                    
            ])
            ->actions($this->getTableActions())
            ->bulkActions([]);
    }

    protected function getTableQuery(): Builder
    {
        return TaskImageReport::query()
            ->latest('date');
    }
}
