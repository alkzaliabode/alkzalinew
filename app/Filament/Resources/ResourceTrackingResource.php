<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourceTrackingResource\Pages;
use App\Models\ResourceTracking;
use App\Models\Unit; // Import the Unit model
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification; // Import Notification

class ResourceTrackingResource extends Resource
{
    protected static ?string $model = ResourceTracking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'تتبع الموارد';
    protected static ?string $modelLabel = 'تتبع الموارد';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('date')
                ->required()
                ->default(now())
                ->label('التاريخ'),

            Forms\Components\Select::make('unit_id')
                ->relationship('unit', 'name')
                ->required()
                ->label('الوحدة')
                ->native(false),

            Forms\Components\TextInput::make('working_hours')
                ->numeric()
                ->required()
                ->minValue(0)
                ->maxValue(24)
                ->label('ساعات العمل الإجمالية'),

            Forms\Components\TextInput::make('cleaning_materials')
                ->numeric()
                ->required()
                ->minValue(0)
                ->suffix('لتر')
                ->label('مواد التنظيف المستهلكة'),

            Forms\Components\TextInput::make('water_consumption')
                ->numeric()
                ->required()
                ->minValue(0)
                ->suffix('لتر')
                ->label('استهلاك المياه'),

            Forms\Components\TextInput::make('equipment_usage')
                ->numeric()
                ->required()
                ->minValue(0)
                ->label('عدد المعدات المستخدمة'),

            Forms\Components\Textarea::make('notes')
                ->label('ملاحظات')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->label('التاريخ'),

                Tables\Columns\TextColumn::make('unit.name')
                    ->sortable()
                    ->label('الوحدة'),

                Tables\Columns\TextColumn::make('working_hours')
                    ->sortable()
                    ->label('ساعات العمل'),

                Tables\Columns\TextColumn::make('cleaning_materials')
                    ->sortable()
                    ->suffix(' لتر')
                    ->label('مواد التنظيف'),

                Tables\Columns\TextColumn::make('equipment_usage')
                    ->sortable()
                    ->label('المعدات'),

                Tables\Columns\TextColumn::make('efficiency')
                    ->label('الكفاءة (مهمة/ساعة)')
                    ->state(function (ResourceTracking $record) {
                        // For demonstration, let's assume 'tasks_count' would come from a real count
                        // If you need actual task counts for this display, you'd need to eager load or perform a subquery.
                        // For now, this is a placeholder/example of how to calculate a simple efficiency.
                        // The 'efficiency' in ActualResult is the more critical one.
                        $completedGeneral = \App\Models\GeneralCleaningTask::where('unit_id', $record->unit_id)
                            ->whereDate('date', $record->date)
                            ->where('status', 'مكتمل')
                            ->count();
                        $completedSanitation = \App\Models\SanitationFacilityTask::where('unit_id', $record->unit_id)
                            ->whereDate('date', $record->date)
                            ->where('status', 'مكتمل')
                            ->count();
                        $totalCompletedTasks = $completedGeneral + $completedSanitation;

                        $hours = $record->working_hours ?? 1; // Prevent division by zero
                        return round($totalCompletedTasks / max($hours, 1), 2);
                    }),

                Tables\Columns\IconColumn::make('is_efficient')
                    ->label('كفء؟')
                    ->boolean()
                    ->state(fn(ResourceTracking $record) =>
                        // This logic needs to be based on an actual definition of 'efficient'
                        // For example: if efficiency (tasks per hour) is >= a certain threshold
                        // Using a placeholder for now, you should define your efficiency threshold.
                        $record->working_hours > 0 && ($record->cleaning_materials + $record->water_consumption + $record->equipment_usage) > 0 // Example: if any resource used
                    ),

                Tables\Columns\TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn(ResourceTracking $record) => $record->notes),
            ])
            ->headerActions([
                Tables\Actions\Action::make('auto_generate_resource_data')
                    ->label('توليد بيانات الموارد تلقائياً')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->action(function () {
                        self::generateDailyResourceData();
                        Notification::make()
                            ->title('تم توليد بيانات الموارد بنجاح')
                            ->success()
                            ->send();
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit')
                    ->relationship('unit', 'name')
                    ->label('الوحدة'),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('to')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('date', '>=', $data['from']))
                            ->when($data['to'], fn($q) => $q->whereDate('date', '<=', $data['to']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResourceTrackings::route('/'),
            'create' => Pages\CreateResourceTracking::route('/create'),
            'edit' => Pages\EditResourceTracking::route('/{record}/edit'),
        ];
    }

    // Function to generate daily resource data for all units
    public static function generateDailyResourceData()
    {
        $units = Unit::all();
        $today = now()->format('Y-m-d');

        foreach ($units as $unit) {
            $existingRecord = ResourceTracking::where('unit_id', $unit->id)
                ->where('date', $today)
                ->first();

            if (!$existingRecord) {
                // Create a new record with default or calculated values
                ResourceTracking::create([
                    'date' => $today,
                    'unit_id' => $unit->id,
                    'working_hours' => 8, // Default working hours
                    'cleaning_materials' => 0, // Default to 0, can be adjusted
                    'water_consumption' => 0, // Default to 0, can be adjusted
                    'equipment_usage' => 0, // Default to 0, can be adjusted
                    'notes' => 'بيانات تم توليدها تلقائياً لـ ' . $unit->name . ' بتاريخ ' . $today,
                ]);
            }
        }
    }
}
