<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SanitationFacilityTaskResource\Pages;
use App\Models\SanitationFacilityTask;
use App\Models\Employee;
use App\Models\Unit;
use App\Models\UnitGoal; // ✅ هام: استيراد موديل UnitGoal

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
                                    ->columnSpan(1),

                                Select::make('shift')
                                    ->options([
                                        'صباحي' => 'صباحي',
                                        'مسائي' => 'مسائي',
                                        'ليلي' => 'ليلي',
                                    ])
                                    ->required()
                                    ->label('الوجبة') // تم تغيير التسمية من "الوجبة" إلى "الوردية" لتكون أوضح
                                    ->columnSpan(1),

                                Select::make('status')
                                    ->options([
                                        'مكتمل' => 'مكتمل',
                                        'قيد التنفيذ' => 'قيد التنفيذ',
                                        'ملغى' => 'ملغى',
                                    ])
                                    ->required()
                                    ->label('الحالة')
                                    ->columnSpan(1),

                                Select::make('unit_id')
                                    ->label('الوحدة')
                                    ->default(fn () => Unit::where('name', 'وحدة المنشآت الصحية')->first()?->id)
                                    ->hidden() // إخفاء الحقل عن المستخدم
                                    ->relationship('unit', 'name')
                                    ->required()
                                    ->columnSpan(1),
                            ]),
                        // ✅ إضافة حقل related_goal_id هنا
                        Select::make('related_goal_id')
                            ->label('الهدف المرتبط')
                            ->relationship('relatedGoal', 'goal_text') // يعرض 'goal_text' من موديل UnitGoal
                            ->searchable() // يسمح بالبحث عن الأهداف
                            ->required() // اجعله مطلوباً لضمان اختيار هدف
                            ->placeholder('اختر الهدف المرتبط'), // نص توضيحي

                        // ✅ حقل ساعات العمل للمهمة تم نقله إلى نهاية قسم "المعلومات الأساسية"
                        TextInput::make('working_hours')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(24) // ساعات العمل لا تتجاوز 24 ساعة
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
                                    ->live(), // مهم لتحديث الحقول بناءً على الاختيار

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
                                    ->searchable(),

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
                            ->hidden(fn ($get) => empty($get('task_type'))),
                    ]),

                Section::make('الموارد المستخدمة')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        // ✅ تم تعديل هذا الجزء ليطابق هيكل جدول النظافة العامة
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
                                    ->required(),
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
                            ->relationship('employeeTasks')
                            ->schema([
                                Select::make('employee_id')
                                    ->label('الموظف')
                                    ->options(fn () => Employee::query()
                                        ->orderBy('name')
                                        ->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
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
                                    ->required(),
                            ])
                            ->columns(3)
                            ->createItemButtonLabel('إضافة منفذ جديد'),
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
                            ->columnSpan(1),

                        FileUpload::make('after_images')
                            ->label('صور بعد التنفيذ')
                            ->image()
                            ->multiple()
                            ->directory('sanitation_facility_tasks/after')
                            ->imageEditor()
                            ->columnSpan(1),
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
                    ->label('الوجبة') // تم تغيير التسمية من "الوجبة" إلى "الوردية"
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'مكتمل',
                        'warning' => 'قيد التنفيذ',
                        'danger' => 'ملغى',
                    ]),

                // ✅ عرض عمود working_hours في الجدول
                Tables\Columns\TextColumn::make('working_hours')
                    ->label('ساعات العمل')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('employeeTasks.employee.name')
                    ->label('المنفذون')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList(),

                // ✅ إضافة عرض حقل الهدف المرتبط في الجدول
                Tables\Columns\TextColumn::make('relatedGoal.goal_text')
                    ->label('الهدف المرتبط')
                    ->words(10)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('task_type')
                    ->label('نوع المهمة')
                    ->options([
                        'إدامة' => 'إدامة',
                        'صيانة' => 'صيانة',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة المهمة')
                    ->options([
                        'مكتمل' => 'مكتمل',
                        'قيد التنفيذ' => 'قيد التنفيذ',
                        'ملغى' => 'ملغى',
                    ]),

                Tables\Filters\SelectFilter::make('shift')
                    ->label('الوجبة') // تم تغيير التسمية من "الوجبة" إلى "الوردية"
                    ->options([
                        'صباحي' => 'صباحي',
                        'مسائي' => 'مسائي',
                        'ليلي' => 'ليلي',
                    ]),

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
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض'),

                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(), // أعدت هذا كإجراء افتراضي
                FilamentExportBulkAction::make('export')
                    ->label('تصدير البيانات'),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('تصدير البيانات'),
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
        $data['unit_id'] = 2;
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
