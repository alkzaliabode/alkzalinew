<?php
// استبدل محتوى ملف ActualResultResource.php الموجود بهذا الكود

namespace App\Filament\Resources;

use App\Models\ActualResult;
use App\Models\UnitGoal;
use App\Models\ResourceTracking;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Notifications\Notification;
use App\Filament\Resources\ActualResultResource\Pages;
use Illuminate\Database\Eloquent\Builder; // Import Builder
use Illuminate\Support\Carbon; // Import Carbon for date handling

class ActualResultResource extends Resource
{
    protected static ?string $model = ActualResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'النتائج الفعلية';
    protected static ?string $navigationGroup = 'إدارة الأداء';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'date';
    protected static bool $shouldRegisterNavigation = true;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('البيانات الأساسية')
                ->schema([
                    Forms\Components\DatePicker::make('date')
                        ->label('التاريخ')
                        ->required()
                        ->default(now())
                        ->reactive() // Make date reactive to update target_tasks if date changes
                        ->afterStateUpdated(fn ($state, callable $set, $get) =>
                            self::autoFillTargetTasks($get('unit_id'), $state, $set))
                        ->columnSpan(1), // Added for better layout if needed

                    Forms\Components\Select::make('unit_id')
                        ->relationship('unit', 'name')
                        ->label('الوحدة')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, $get) =>
                            self::autoFillTargetTasks($state, $get('date'), $set))
                        ->columnSpan(1), // Added for better layout if needed

                    // هذا الحقل لعرض المهام المستهدفة من UnitGoal في الفورم
                    Forms\Components\TextInput::make('target_tasks_display')
                        ->label('المهام المستهدفة (من الهدف)')
                        ->disabled()
                        ->dehydrated(false) // Do not save this field to the database
                        ->helperText('يتم جلبها تلقائياً من جدول أهداف الوحدة'),

                    Forms\Components\TextInput::make('completed_tasks')
                        ->label('المهام المكتملة')
                        ->numeric()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            self::calculatePerformanceMetrics($get, $set);
                        }),

                    Forms\Components\TextInput::make('working_hours')
                        ->label('ساعات العمل')
                        ->numeric()
                        ->default(8)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            self::calculatePerformanceMetrics($get, $set);
                        }),
                ])->columns(2), // Make basic data section two columns

            Forms\Components\Section::make('مثلث الأداء Gilbert')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('effectiveness')
                            ->label('الفعالية %')
                            ->disabled()
                            ->suffix('%')
                            ->helperText('النتائج ÷ الأهداف')
                            ->extraAttributes(['class' => 'text-green-600 font-bold']),

                        Forms\Components\TextInput::make('efficiency')
                            ->label('الكفاءة %')
                            ->disabled()
                            ->suffix('%')
                            ->helperText('النتائج ÷ الموارد')
                            ->extraAttributes(['class' => 'text-blue-600 font-bold']),

                        Forms\Components\TextInput::make('relevance')
                            ->label('الملاءمة %')
                            ->disabled()
                            ->suffix('%')
                            ->helperText('الموارد ÷ الأهداف')
                            ->extraAttributes(['class' => 'text-purple-600 font-bold']),
                    ]),

                    Forms\Components\TextInput::make('overall_performance_score')
                        ->label('نقاط الأداء الإجمالية')
                        ->disabled()
                        ->helperText('متوسط الأبعاد الثلاثة')
                        ->extraAttributes(['class' => 'text-red-600 font-bold text-lg']),
                ]),

            Forms\Components\Section::make('بيانات إضافية')
                ->schema([
                    Forms\Components\Select::make('quality_rating')
                        ->label('تقييم الجودة')
                        ->options([
                            1 => '⭐ ضعيف',
                            2 => '⭐⭐ مقبول',
                            3 => '⭐⭐⭐ جيد',
                            4 => '⭐⭐⭐⭐ ممتاز',
                            5 => '⭐⭐⭐⭐⭐ استثنائي',
                        ])
                        ->default(3),

                    Forms\Components\TextInput::make('efficiency_score')
                        ->label('درجة الكفاءة (1-100)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(100)
                        ->helperText('درجة إضافية لتقييم الكفاءة، يدوية الإدخال'), // Added helper text to clarify

                    Forms\Components\Textarea::make('notes')
                        ->label('ملاحظات')
                        ->rows(3),
                ]),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date('Y-m-d')
                    ->label('التاريخ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit.name')
                    ->label('الوحدة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('unitGoal.target_tasks')
                    ->label('المستهدف')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('completed_tasks')
                    ->label('المكتمل')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('effectiveness')
                    ->label('الفعالية')
                    ->suffix('%')
                    ->alignCenter()
                    ->color(fn ($record) => self::getPerformanceColor($record->effectiveness))
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('efficiency')
                    ->label('الكفاءة')
                    ->suffix('%')
                    ->alignCenter()
                    ->color(fn ($record) => self::getPerformanceColor($record->efficiency))
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('relevance')
                    ->label('الملاءمة')
                    ->suffix('%')
                    ->alignCenter()
                    ->color(fn ($record) => self::getPerformanceColor($record->relevance))
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('overall_performance_score')
                    ->label('الأداء الإجمالي')
                    ->suffix('%')
                    ->alignCenter()
                    ->color(fn ($record) => self::getOverallPerformanceColor($record->overall_performance_score))
                    ->weight('bold')
                    ->size('lg'),

                Tables\Columns\IconColumn::make('quality_rating')
                    ->label('الجودة')
                    ->icon(fn ($state) => 'heroicon-o-star')
                    ->color('warning')
                    ->size('sm'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('auto_generate_results')
                    ->label('توليد النتائج تلقائياً')
                    ->icon('heroicon-o-cog-8-tooth')
                    ->color('success')
                    ->action(function () {
                        self::generateDailyResults();
                        Notification::make()
                            ->title('تم توليد النتائج بنجاح')
                            ->success()
                            ->send();
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit_id')
                    ->relationship('unit', 'name')
                    ->label('الوحدة'),

                Tables\Filters\Filter::make('performance_range')
                    ->form([
                        Forms\Components\Select::make('performance_level')
                            ->label('مستوى الأداء')
                            ->options([
                                'excellent' => 'ممتاز (90% فأكثر)',
                                'good' => 'جيد (70-89%)',
                                'average' => 'متوسط (50-69%)',
                                'poor' => 'ضعيف (أقل من 50%)',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['performance_level']) {
                            return $query;
                        }

                        return match ($data['performance_level']) {
                            'excellent' => $query->where('overall_performance_score', '>=', 90),
                            'good' => $query->whereBetween('overall_performance_score', [70, 89]),
                            'average' => $query->whereBetween('overall_performance_score', [50, 69]),
                            'poor' => $query->where('overall_performance_score', '<', 50),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('recalculate_performance')
                        ->label('إعادة حساب الأداء')
                        ->icon('heroicon-o-calculator')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                // Call the static method from the ActualResult model to recalculate and update
                                \App\Models\ActualResult::recalculateForUnitAndDate($record->unit_id, $record->date);
                            }
                            Notification::make()
                                ->title('تم إعادة حساب الأداء للسجلات المحددة')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    /**
     * تُستخدم لملء حقل "المهام المستهدفة" في الفورم تلقائياً
     * عند اختيار الوحدة أو تغيير التاريخ.
     */
    protected static function autoFillTargetTasks(?int $unitId, ?string $date, callable $set)
    {
        if (!$unitId || !$date) {
            $set('target_tasks_display', 0);
            return;
        }

        $parsedDate = Carbon::parse($date)->format('Y-m-d');

        $goal = UnitGoal::where('unit_id', $unitId)
            ->where('date', $parsedDate)
            ->first();

        if ($goal) {
            $set('target_tasks_display', $goal->target_tasks);
        } else {
            $set('target_tasks_display', 0);
        }
        // بعد تحديث المهام المستهدفة، أعد حساب المقاييس
        self::calculatePerformanceMetrics(fn($field) => $set($field, $set($field, $field)), $set);
    }

    /**
     * تُستخدم لحساب مقاييس الأداء في الفورم بشكل تفاعلي
     * عند تغيير المهام المكتملة أو ساعات العمل.
     */
    protected static function calculatePerformanceMetrics(callable $get, callable $set)
    {
        // نستخدم 'target_tasks_display' لأنه يمثل القيمة المعروضة والمجلوبة من UnitGoal
        $targetTasks = (float) $get('target_tasks_display') ?: 0;
        $completedTasks = (float) $get('completed_tasks') ?: 0;
        // تأكد من أن working_hours لا يساوي صفر لتجنب القسمة على صفر في الكفاءة
        $workingHours = (float) $get('working_hours') ?: 1;

        // Ensure targetTasksForCalculation is at least 1 to avoid division by zero for effectiveness/relevance
        $targetTasksForCalculation = ($targetTasks === 0.0) ? 1 : $targetTasks;


        $effectiveness = ($targetTasksForCalculation > 0) ? ($completedTasks / $targetTasksForCalculation) * 100 : 0;
        $efficiency = ($workingHours > 0) ? ($completedTasks / $workingHours) * 100 : 0;
        $relevance = ($targetTasksForCalculation > 0) ? ($workingHours / $targetTasksForCalculation) * 100 : 0;

        // توحيد منطق حساب الأداء الإجمالي مع الموديل: متوسط المقاييس غير الصفرية
        $overallScore = 0;
        $validMetricsCount = 0;
        if ($effectiveness > 0) { $overallScore += $effectiveness; $validMetricsCount++; }
        if ($efficiency > 0) { $overallScore += $efficiency; $validMetricsCount++; }
        if ($relevance > 0) { $overallScore += $relevance; $validMetricsCount++; }
        $overallScore = ($validMetricsCount > 0) ? ($overallScore / $validMetricsCount) : 0;

        $set('effectiveness', round($effectiveness, 2));
        $set('efficiency', round($efficiency, 2));
        $set('relevance', round($relevance, 2));
        $set('overall_performance_score', round($overallScore, 2));
    }

    protected static function getPerformanceColor($value): string
    {
        return match (true) {
            $value >= 90 => 'success',
            $value >= 70 => 'warning',
            $value >= 50 => 'info',
            default => 'danger',
        };
    }

    protected static function getOverallPerformanceColor($value): string
    {
        return match (true) {
            $value >= 85 => 'success',
            $value >= 65 => 'warning',
            default => 'danger',
        };
    }

    /**
     * دالة لتوليد/تحديث النتائج الفعلية اليومية لجميع الوحدات.
     * تستدعي ActualResult::recalculateForUnitAndDate لكل وحدة وتاريخ اليوم.
     */
    public static function generateDailyResults()
    {
        $units = \App\Models\Unit::all();
        $today = now()->format('Y-m-d'); // التأكد من تنسيق التاريخ الصحيح

        foreach ($units as $unit) {
            // استخدام دالة الموديل الموحدة للحساب والتحديث/الإنشاء
            \App\Models\ActualResult::recalculateForUnitAndDate($unit->id, $today);
        }
    }

    // هذه الدالة تم دمج منطقها في استدعاء ActualResult::recalculateForUnitAndDate
    // ضمن الإجراء الجماعي 'recalculate_performance'
    // لذا لم تعد هناك حاجة لدالة منفصلة هنا.
    // protected static function recalculatePerformanceForRecord($record) { ... }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActualResults::route('/'),
            'create' => Pages\CreateActualResult::route('/create'),
            'edit' => Pages\EditActualResult::route('/{record}/edit'),
        ];
    }
}
