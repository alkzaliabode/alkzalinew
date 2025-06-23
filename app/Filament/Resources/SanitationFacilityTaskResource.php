<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SanitationFacilityTaskResource\Pages;
use App\Models\SanitationFacilityTask;
use App\Models\Employee;
use App\Models\Unit;
use App\Models\UnitGoal;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder; // تم إضافة استيراد Builder للفلاتر

class SanitationFacilityTaskResource extends Resource
{
    protected static ?string $model = SanitationFacilityTask::class;

    protected static ?string $navigationGroup = 'وحدة المنشآت الصحية';
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationLabel = 'مهام المنشآت الصحية';
    protected static ?string $modelLabel = 'مهمة منشأة صحية';
    protected static ?string $pluralModelLabel = 'مهام المنشآت الصحية';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('المعلومات الأساسية')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                DatePicker::make('date')
                                    ->required()
                                    ->label('التاريخ')
                                    ->default(now())
                                    ->native(false) // للحصول على مظهر FilamentDatePicker
                                    ->displayFormat('Y-m-d')
                                    ->columnSpan(1),

                                Select::make('shift')
                                    ->options([
                                        'صباحي' => 'صباحي',
                                        'مسائي' => 'مسائي',
                                        'ليلي' => 'ليلي',
                                    ])
                                    ->required()
                                    ->label('الوجبة')
                                    ->native(false) // للحصول على مظهر FilamentSelect
                                    ->columnSpan(1),

                                Select::make('status')
                                    ->options([
                                        'مكتمل' => 'مكتمل',
                                        'قيد التنفيذ' => 'قيد التنفيذ',
                                        'ملغى' => 'ملغى',
                                    ])
                                    ->required()
                                    ->label('الحالة')
                                    ->native(false) // للحصول على مظهر FilamentSelect
                                    ->columnSpan(1),

                                // حقل unit_id مخفي ويتم تعيين قيمته تلقائياً بواسطة mutateFormDataBeforeCreate
                                Select::make('unit_id')
                                    ->label('الوحدة')
                                    ->default(fn () => Unit::where('name', 'وحدة المنشآت الصحية')->first()?->id) // تعيين افتراضي
                                    ->hidden() // إخفاء الحقل عن المستخدم
                                    ->relationship('unit', 'name')
                                    ->required()
                                    ->columnSpan(1),

                                // حقول created_by و updated_by مخفية تماماً، وتتم تعبئتها عبر أحداث الموديل (booted method)
                                TextInput::make('created_by')
                                    ->hidden(true)
                                    ->dehydrated(false), // يمنع إرسال قيمته إذا كان مخفياً
                                TextInput::make('updated_by')
                                    ->hidden(true)
                                    ->dehydrated(false), // يمنع إرسال قيمته إذا كان مخفياً
                            ]),
                        
                        Select::make('related_goal_id')
                            ->label('الهدف المرتبط')
                            ->relationship('relatedGoal', 'goal_text') // يعرض 'goal_text' من موديل UnitGoal
                            ->searchable() // يسمح بالبحث عن الأهداف
                            ->required() // اجعله مطلوباً لضمان اختيار هدف
                            ->placeholder('اختر الهدف المرتبط') // نص توضيحي
                            ->helperText('اختر الهدف الاستراتيجي أو التشغيلي الذي تساهم فيه هذه المهمة.')
                            ->native(false), // للحصول على مظهر FilamentSelect

                        TextInput::make('working_hours')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(24) // ساعات العمل لا تتجاوز 24 ساعة منطقياً
                            ->label('إجمالي ساعات العمل للمهمة')
                            ->helperText('إجمالي ساعات العمل التي استغرقتها هذه المهمة.')
                            ->required(),
                    ]),

                Section::make('تفاصيل المهمة')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('task_type')
                                    ->options([
                                        'إدامة' => 'إدامة',
                                        'صيانة' => 'صيانة',
                                    ])
                                    ->required()
                                    ->label('نوع المهمة')
                                    ->live() // مهم لتحديث الحقول بناءً على الاختيار
                                    ->native(false),

                                Select::make('facility_name')
                                    ->options([
                                        'صحية الجامع رجال' => 'صحية الجامع رجال',
                                        'صحية الجامع نساء' => 'صحية الجامع نساء',
                                        'صحية 1 رجال' => 'صحية 1 رجال',
                                        'صحية 2 رجال' => 'صحية 2 رجال',
                                        'صحية 3 رجال' => 'صحية 3 رجال',
                                        'صحية 4 رجال' => 'صحية 4 رجال',
                                        'صحية 1 نساء' => 'صحية 1 نساء',
                                        'صحية 2 نساء' => 'صحية 2 نساء',
                                        'صحية 3 نساء' => 'صحية 3 نساء',
                                        'صحية 4 نساء' => 'صحية 4 نساء',
                                        'المجاميع الكبيرة رجال' => 'المجاميع الكبيرة رجال',
                                        'المجاميع الكبيرة نساء' => 'المجاميع الكبيرة نساء',
                                    ])
                                    ->required()
                                    ->label('اسم المرفق الصحي')
                                    ->searchable()
                                    ->native(false),

                                Textarea::make('details')
                                    ->label('تفاصيل إضافية')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                        Fieldset::make('تفاصيل المعدات')
                            ->schema(function ($get) {
                                $fields = [];
                                $taskType = $get('task_type');
                                $prefix = $taskType === 'إدامة' ? 'عدد' : 'عدد';
                                $suffix = $taskType === 'إدامة' ? 'المدامة' : 'المصانة';

                                $fields[] = Grid::make(4)
                                    ->schema([
                                        TextInput::make('seats_count')
                                            ->numeric()->minValue(0)->label("{$prefix} المقاعد {$suffix}")->columnSpan(1),
                                        TextInput::make('mirrors_count')
                                            ->numeric()->minValue(0)->label("{$prefix} المرايا {$suffix}")->columnSpan(1),
                                        TextInput::make('mixers_count')
                                            ->numeric()->minValue(0)->label("{$prefix} الخلاطات {$suffix}")->columnSpan(1),
                                        TextInput::make('doors_count')
                                            ->numeric()->minValue(0)->label("{$prefix} الأبواب {$suffix}")->columnSpan(1),
                                        TextInput::make('sinks_count')
                                            ->numeric()->minValue(0)->label("{$prefix} المغاسل {$suffix}")->columnSpan(1),
                                        TextInput::make('toilets_count')
                                            ->numeric()->minValue(0)->label("{$prefix} الحمامات {$suffix}")->columnSpan(1),
                                    ]);

                                return $fields;
                            })
                            ->hidden(fn ($get) => empty($get('task_type'))), // إخفاء الحقل حتى يتم اختيار نوع المهمة
                    ]),

                Section::make('الموارد المستخدمة')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('resources_used')
                            ->label('الموارد المستخدمة')
                            ->schema([
                                TextInput::make('name')
                                    ->label('اسم المورد')
                                    ->required()
                                    ->columnSpan(2),

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->minValue(0)
                                    ->label('الكمية')
                                    ->required(),

                                Select::make('unit')
                                    ->label('وحدة القياس')
                                    ->options([
                                        'قطعة' => 'قطعة',
                                        'كرتون' => 'كرتون',
                                        'رول' => 'رول',
                                        'لتر' => 'لتر',
                                        'عبوة' => 'عبوة',
                                        'أخرى' => 'أخرى',
                                    ])
                                    ->required()
                                    ->native(false),
                            ])
                            ->columns(4) // أصبحت 4 أعمدة
                            ->createItemButtonLabel('إضافة مورد جديد')
                            ->defaultItems(0),
                    ]),

                Section::make('المنفذون والتقييم')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('employeeTasks')
                            ->label('الموظفون المنفذون وتقييمهم')
                            ->relationship('employeeTasks') // علاقة HasMany مع EmployeeTask
                            ->schema([
                                Select::make('employee_id')
                                    ->label('الموظف')
                                    ->options(fn () => Employee::query()
                                        ->orderBy('name')
                                        ->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(2),

                                Select::make('employee_rating')
                                    ->label('تقييم الأداء')
                                    ->options([
                                        1 => '★ (ضعيف)',
                                        2 => '★★',
                                        3 => '★★★ (متوسط)',
                                        4 => '★★★★',
                                        5 => '★★★★★ (ممتاز)',
                                    ])
                                    ->required()
                                    ->native(false),
                            ])
                            ->columns(3)
                            ->createItemButtonLabel('إضافة منفذ جديد')
                            ->defaultItems(0), // لتجنب إضافة عنصر فارغ تلقائياً
                    ]),

                Section::make('المرفقات')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        FileUpload::make('before_images')
                            ->label('صور قبل التنفيذ')
                            ->image()
                            ->multiple()
                            ->directory('sanitation_facility_tasks/before')
                            ->imageEditor()
                            ->columnSpan(1)
                            ->helperText('يمكنك رفع عدة صور توضح حالة الموقع قبل بدء المهمة.'),

                        FileUpload::make('after_images')
                            ->label('صور بعد التنفيذ')
                            ->image()
                            ->multiple()
                            ->directory('sanitation_facility_tasks/after')
                            ->imageEditor()
                            ->columnSpan(1)
                            ->helperText('يمكنك رفع عدة صور توضح حالة الموقع بعد انتهاء المهمة.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('facility_name')
                    ->label('المرفق الصحي')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('task_type')
                    ->label('نوع المهمة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'إدامة' => 'info',
                        'صيانة' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('shift')
                    ->label('الوجبة')
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'مكتمل',
                        'warning' => 'قيد التنفيذ',
                        'danger' => 'ملغى',
                    ]),

                Tables\Columns\TextColumn::make('working_hours')
                    ->label('ساعات العمل')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                
                // عرض الموظف الذي أنشأ المهمة والموظف الذي عدلها
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('أنشأها المشرف')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // مخفي افتراضياً في الجدول
                Tables\Columns\TextColumn::make('editor.name')
                    ->label('عدّلها المشرف')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // مخفي افتراضياً في الجدول

                Tables\Columns\TextColumn::make('employeeTasks.employee.name')
                    ->label('المنفذون والتقييم')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->formatStateUsing(function ($state, $record) {
                        $summary = '';
                        foreach ($record->employeeTasks as $employeeTask) {
                            $employeeName = $employeeTask->employee->name ?? 'غير معروف';
                            $rating = $employeeTask->employee_rating;
                            $ratingText = match ((int)$rating) { // تأكد أن التقييم عدد صحيح
                                1 => 'ضعيف ★',
                                2 => '★★',
                                3 => 'متوسط ★★★',
                                4 => '★★★★',
                                5 => 'ممتاز ★★★★★',
                                default => 'غير مقيم',
                            };
                            $summary .= '<div class="flex items-center gap-1">' . $employeeName . ' (<span class="font-bold">' . $ratingText . '</span>)</div>';
                        }
                        return new HtmlString($summary);
                    }),


                Tables\Columns\TextColumn::make('relatedGoal.goal_text')
                    ->label('الهدف المرتبط')
                    ->words(10)
                    ->wrap()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('unit.name')
                    ->label('الوحدة')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\ImageColumn::make('before_images')
                    ->label('صور قبل')
                    ->height(80)
                    ->width(80)
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->toggleable(),

                Tables\Columns\ImageColumn::make('after_images')
                    ->label('صور بعد')
                    ->height(80)
                    ->width(80)
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->toggleable(),
                
                // أعمدة العدادات الخاصة بالمنشآت الصحية (مخفية افتراضياً)
                Tables\Columns\TextColumn::make('seats_count')
                    ->label('المقاعد')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mirrors_count')
                    ->label('المرايا')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mixers_count')
                    ->label('الخلاطات')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('doors_count')
                    ->label('الأبواب')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sinks_count')
                    ->label('المغاسل')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('toilets_count')
                    ->label('الحمامات')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // عرض الموارد المستخدمة
                Tables\Columns\TextColumn::make('resources_used')
                    ->label('الموارد المستخدمة')
                    ->formatStateUsing(function (?array $state): HtmlString {
                        if (empty($state)) {
                            return new HtmlString('لا توجد موارد');
                        }
                        $items = [];
                        foreach ($state as $resource) {
                            $name = $resource['name'] ?? 'غير محدد';
                            $quantity = $resource['quantity'] ?? 'غير محدد';
                            $unit = $resource['unit'] ?? '';
                            $items[] = "{$name} ({$quantity} {$unit})";
                        }
                        return new HtmlString(implode('<br>', $items));
                    })
                    ->listWithLineBreaks()
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),


                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ آخر تحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('task_type')
                    ->label('نوع المهمة')
                    ->options([
                        'إدامة' => 'إدامة',
                        'صيانة' => 'صيانة',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة المهمة')
                    ->options([
                        'مكتمل' => 'مكتمل',
                        'قيد التنفيذ' => 'قيد التنفيذ',
                        'ملغى' => 'ملغى',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('shift')
                    ->label('الوجبة')
                    ->options([
                        'صباحي' => 'صباحي',
                        'مسائي' => 'مسائي',
                        'ليلي' => 'ليلي',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('facility_name')
                    ->label('المرفق الصحي')
                    ->options([
                        'صحية الجامع رجال' => 'صحية الجامع رجال',
                        'صحية الجامع نساء' => 'صحية الجامع نساء',
                        'صحية 1 رجال' => 'صحية 1 رجال',
                        'صحية 2 رجال' => 'صحية 2 رجال',
                        'صحية 3 رجال' => 'صحية 3 رجال',
                        'صحية 4 رجال' => 'صحية 4 رجال',
                        'صحية 1 نساء' => 'صحية 1 نساء',
                        'صحية 2 نساء' => 'صحية 2 نساء',
                        'صحية 3 نساء' => 'صحية 3 نساء',
                        'صحية 4 نساء' => 'صحية 4 نساء',
                        'المجاميع الكبيرة رجال' => 'المجاميع الكبيرة رجال',
                        'المجاميع الكبيرة نساء' => 'المجاميع الكبيرة نساء',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('الموظف المنفذ')
                    ->relationship('employeeTasks.employee', 'name') // العلاقة الصحيحة
                    ->searchable()
                    ->preload()
                    ->native(false),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        DatePicker::make('from_date')->label('من تاريخ')->native(false)->displayFormat('Y-m-d'),
                        DatePicker::make('to_date')->label('إلى تاريخ')->native(false)->displayFormat('Y-m-d'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض'),

                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                FilamentExportBulkAction::make('export')
                    ->label('تصدير المحدد'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إنشاء مهمة جديدة'),
                FilamentExportHeaderAction::make('export')
                    ->label('تصدير الكل'),
            ]);
    }

    /**
     * تقوم بتعديل البيانات قبل أن يتم حفظها عند الإنشاء.
     * هذا يضمن تعيين 'unit_id' تلقائيًا دون تدخل المستخدم.
     *
     * @param array $data البيانات المرسلة من النموذج.
     * @return array البيانات المعدلة.
     */
    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        // تعيين unit_id لوحدة "المنشآت الصحية" (ID: 2) تلقائياً
        // تأكد أن الـ ID=2 هو فعلاً لوحدة المنشآت الصحية في جدول units
        $data['unit_id'] = 2; // أو يمكن استخدام Unit::where('name', 'وحدة المنشآت الصحية')->first()?->id
        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSanitationFacilityTasks::route('/'),
            'create' => Pages\CreateSanitationFacilityTask::route('/create'),
            'edit' => Pages\EditSanitationFacilityTask::route('/{record}/edit'),
        ];
    }
}
