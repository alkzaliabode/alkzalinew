<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GeneralCleaningTaskResource\Pages;
use App\Models\GeneralCleaningTask;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;

class GeneralCleaningTaskResource extends Resource
{
    protected static ?string $model = GeneralCleaningTask::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'وحدة النظافة العامة';
    protected static ?string $navigationLabel = 'مهام النظافة العامة';
    protected static ?string $modelLabel = 'مهمة نظافة عامة';
    protected static ?string $pluralModelLabel = 'مهام النظافة العامة';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('المعلومات الأساسية')
                    ->schema([
                        // ✅ تم تعديل هذه الحقول لتكون مخفية تمامًا ولا يتم تعبئتها من الفورم مباشرة
                        Grid::make(2)
                            ->schema([
                                TextInput::make('created_by')
                                    ->hidden(true)
                                    ->dehydrated(false), // يمنع إرسال قيمته إذا كان مخفياً
                                TextInput::make('updated_by')
                                    ->hidden(true)
                                    ->dehydrated(false), // يمنع إرسال قيمته إذا كان مخفياً
                            ]),
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('date')
                                    ->required()
                                    ->label('التاريخ')
                                    ->default(now())
                                    ->native(false)
                                    ->displayFormat('Y-m-d'),

                                Select::make('shift')
                                    ->options([
                                        'صباحي' => 'صباحي',
                                        'مسائي' => 'مسائي',
                                        'ليلي' => 'ليلي',
                                    ])
                                    ->required()
                                    ->label('الوجبة')
                                    ->native(false),

                                Select::make('status')
                                    ->options([
                                        'مكتمل' => 'مكتمل',
                                        'قيد التنفيذ' => 'قيد التنفيذ',
                                        'ملغى' => 'ملغى',
                                    ])
                                    ->required()
                                    ->label('الحالة')
                                    ->native(false),
                            ]),
                        
                        Select::make('related_goal_id')
                            ->label('الهدف المرتبط')
                            ->relationship('relatedGoal', 'goal_text')
                            ->searchable()
                            ->required()
                            ->placeholder('اختر الهدف المرتبط')
                            ->helperText('اختر الهدف الاستراتيجي أو التشغيلي الذي تساهم فيه هذه المهمة.')
                            ->native(false),

                        Select::make('unit_id')
                            ->label('الوحدة')
                            ->default(fn () => Unit::where('name', 'وحدة النظافة العامة')->first()?->id)
                            ->hidden()
                            ->relationship('unit', 'name')
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
                                    ->live()
                                    ->native(false),

                                Select::make('location')
                                    ->options([
                                        // القاعات
                                        'قاعة 1 الأسفل' => 'قاعة 1 الأسفل',
                                        'قاعة 1 الأعلى' => 'قاعة 1 الأعلى',
                                        'قاعة 2 الأسفل' => 'قاعة 2 الأسفل',
                                        'قاعة 2 الأعلى' => 'قاعة 2 الأعلى',
                                        'قاعة 3 الأسفل' => 'قاعة 3 الأسفل',
                                        'قاعة 3 الأعلى' => 'قاعة 3 الأعلى',
                                        'قاعة 4 الأسفل' => 'قاعة 4 الأسفل',
                                        'قاعة 4 الأعلى' => 'قاعة 4 الأعلى',
                                        'قاعة 5 الأسفل' => 'قاعة 5 الأسفل',
                                        'قاعة 5 الأعلى' => 'قاعة 5 الأعلى',
                                        'قاعة 6 الأسفل' => 'قاعة 6 الأسفل',
                                        'قاعة 6 الأعلى' => 'قاعة 6 الأعلى',
                                        'قاعة 7 الأسفل' => 'قاعة 7 الأسفل',
                                        'قاعة 7 الأعلى' => 'قاعة 7 الأعلى',
                                        'قاعة 8 الأسفل' => 'قاعة 8 الأسفل',
                                        'قاعة 8 الأعلى' => 'قاعة 8 الأعلى',
                                        'قاعة 9 الأسفل' => 'قاعة 9 الأسفل',
                                        'قاعة 9 الأعلى' => 'قاعة 9 الأعلى',
                                        'قاعة 10 الأسفل' => 'قاعة 10 الأسفل',
                                        'قاعة 10 الأعلى' => 'قاعة 10 الأعلى',
                                        'قاعة 11 الأسفل' => 'قاعة 11 الأسفل',
                                        'قاعة 11 الأعلى' => 'قاعة 11 الأعلى',
                                        'قاعة 12 الأسفل' => 'قاعة 12 الأسفل',
                                        'قاعة 12 الأعلى' => 'قاعة 12 الأعلى',
                                        'قاعة 13 الأسفل' => 'قاعة 13 الأسفل',
                                        'قاعة 13 الأعلى' => 'قاعة 13 الأعلى',
                                        // المناطق الخارجية
                                        'جميع القواطع الخارجية' => 'جميع القواطع الخارجية',
                                        'الترامز' => 'الترامز',
                                        'السجاد' => 'السجاد',
                                        'الحاويات' => 'الحاويات',
                                        'الجامع' => 'الجامع',
                                        'المركز الصحي' => 'المركز الصحي',
                                    ])
                                    ->required()
                                    ->label('الموقع')
                                    ->searchable()
                                    ->live()
                                    ->native(false),
                            ]),

                        Fieldset::make('تفاصيل التنفيذ')
                            ->schema(function ($get) {
                                $location = $get('location');
                                $fields = [];

                                if (str_contains($location, 'قاعة')) {
                                    $fields[] = Grid::make(4)
                                        ->schema([
                                            TextInput::make('mats_count')
                                                ->numeric()->minValue(0)->label('عدد المنادر المدامة')->columnSpan(1),
                                            TextInput::make('pillows_count')
                                                ->numeric()->minValue(0)->label('عدد الوسادات المدامة')->columnSpan(1),
                                            TextInput::make('fans_count')
                                                ->numeric()->minValue(0)->label('عدد المراوح المدامة')->columnSpan(1),
                                            TextInput::make('windows_count')
                                                ->numeric()->minValue(0)->label('عدد النوافذ المدامة')->columnSpan(1),
                                            TextInput::make('carpets_count')
                                                ->numeric()->minValue(0)->label('عدد السجاد المدام')->columnSpan(1),
                                            TextInput::make('blankets_count')
                                                ->numeric()->minValue(0)->label('عدد البطانيات المدامة')->columnSpan(1),
                                            TextInput::make('beds_count')
                                                ->numeric()->minValue(0)->label('عدد الأسرة')->columnSpan(1),
                                            TextInput::make('beneficiaries_count')
                                                ->numeric()->minValue(0)->label('عدد المستفيدين من القاعة')->columnSpan(1),
                                        ]);
                                } elseif ($location === 'الترامز') {
                                    $fields[] = Grid::make(2)
                                        ->schema([
                                            TextInput::make('filled_trams_count')
                                                ->numeric()->minValue(0)->label('عدد الترامز المملوئة والمدامة')->columnSpan(1),
                                        ]);
                                } elseif ($location === 'السجاد') {
                                    $fields[] = Grid::make(2)
                                        ->schema([
                                            TextInput::make('carpets_laid_count')
                                                ->numeric()->minValue(0)->label('عدد السجاد المفروش في الساحات')->columnSpan(1),
                                        ]);
                                } elseif ($location === 'الحاويات') {
                                    $fields[] = Grid::make(2)
                                        ->schema([
                                            TextInput::make('large_containers_count')
                                                ->numeric()->minValue(0)->label('عدد الحاويات الكبيرة المفرغة والمدامة')->columnSpan(1),
                                            TextInput::make('small_containers_count')
                                                ->numeric()->minValue(0)->label('عدد الحاويات الصغيرة المفرغة والمدامة')->columnSpan(1),
                                        ]);
                                } elseif ($location === 'الجامع' || $location === 'المركز الصحي' || $location === 'جميع القواطع الخارجية') {
                                    $fields[] = Grid::make(2)
                                        ->schema([
                                            TextInput::make('maintenance_details')
                                                ->label('تفاصيل الإدامة اليومية')
                                                ->columnSpan(2),
                                        ]);
                                }

                                return $fields;
                            })
                            ->hidden(fn ($get) => empty($get('location'))),

                        Fieldset::make('تفاصيل القواطع الخارجية')
                            ->schema([
                                TextInput::make('external_partitions_count')
                                    ->numeric()
                                    ->minValue(0)
                                    ->label('عدد القواطع الخارجية المدامة')
                                    ->hidden(fn ($get) => $get('location') !== 'جميع القواطع الخارجية'),
                            ])
                            ->hidden(fn ($get) => $get('location') !== 'جميع القواطع الخارجية'),
                    ]),

                Section::make('الموارد المستخدمة وساعات العمل')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('working_hours')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(24)
                            ->label('إجمالي ساعات العمل للمهمة')
                            ->helperText('إجمالي ساعات العمل التي استغرقتها هذه المهمة.')
                            ->required(),

                        Repeater::make('resources_used')
                            ->label('الموارد الأخرى المستخدمة')
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
                            ->columns(4)
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
                                    ->columnSpan(2)
                                    ->native(false),

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
                            ->directory('general_cleaning_tasks/before')
                            ->imageEditor()
                            ->columnSpan(1)
                            ->helperText('يمكنك رفع عدة صور توضح حالة الموقع قبل بدء المهمة.'),

                        FileUpload::make('after_images')
                            ->label('صور بعد التنفيذ')
                            ->image()
                            ->multiple()
                            ->directory('general_cleaning_tasks/after')
                            ->imageEditor()
                            ->columnSpan(1)
                            ->helperText('يمكنك رفع عدة صور توضح حالة الموقع بعد انتهاء المهمة.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),
                // أضف هذه الأعمدة في قائمة الأعمدة
                TextColumn::make('creator.name')
                    ->label('أنشأها المشرف')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // يمكن إخفاؤها افتراضياً
                TextColumn::make('editor.name')
                    ->label('عدّلها المشرف')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // يمكن إخفاؤها افتراضياً

                TextColumn::make('task_type')
                    ->label('نوع المهمة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'إدامة' => 'info',
                        'صيانة' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('location')
                    ->label('الموقع')
                    ->searchable()
                    ->sortable()
                    ->html()
                    ->formatStateUsing(function (string $state): HtmlString {
                        $iconSvg = '
                            <svg class="h-5 w-5 text-blue-600 transition duration-300 transform group-hover:scale-125" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18L10.5 20.25M12 10.5H16.5M16.5 6V12M16.5 6L14.25 3.75M9.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10.5M15.75 21H10.5V18.75H15.75V21Z" />
                            </svg>
                        ';

                        return new HtmlString(
                            '<div x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false" class="group inline-flex items-center justify-center relative cursor-pointer min-w-[70px] h-full overflow-hidden">' .
                                '<span x-show="!hovered" x-transition:opacity.duration.300 class="text-gray-800 text-center w-full">' . $state . '</span>' .
                                '<span x-show="hovered" x-transition:opacity.duration.300.delay-50 class="absolute inset-0 flex items-center justify-center w-full h-full">' . $iconSvg . '</span>' .
                            '</div>'
                        );
                    }),

                TextColumn::make('shift')
                    ->label('الوجبة')
                    ->toggleable(),

                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'مكتمل',
                        'warning' => 'قيد التنفيذ',
                        'danger' => 'ملغى',
                    ]),

                TextColumn::make('external_partitions_count')
                    ->label('القواطع الخارجية')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('before_images')
                    ->label('صور قبل')
                    ->height(80)
                    ->width(80)
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->toggleable(),

                ImageColumn::make('after_images')
                    ->label('صور بعد')
                    ->height(80)
                    ->width(80)
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->toggleable(),

                TextColumn::make('mats_count')
                    ->label('المنادر المدامة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('pillows_count')
                    ->label('الوسادات المدامة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('fans_count')
                    ->label('المراوح المدامة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('windows_count')
                    ->label('النوافذ المدامة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('carpets_count')
                    ->label('السجاد المدام')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('blankets_count')
                    ->label('البطانيات المدامة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('beds_count')
                    ->label('الأسرة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('beneficiaries_count')
                    ->label('المستفيدون')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('filled_trams_count')
                    ->label('الترامز المملوئة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('carpets_laid_count')
                    ->label('السجاد المفروش')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('large_containers_count')
                    ->label('الحاويات الكبيرة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('small_containers_count')
                    ->label('الحاويات الصغيرة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('maintenance_details')
                    ->label('تفاصيل الإدامة')
                    ->words(10)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('working_hours')
                    ->label('ساعات العمل')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('resources_used')
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

                TextColumn::make('employeeTasks.employee.name')
                    ->label('المنفذون والتقييم')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->formatStateUsing(function ($state, $record) {
                        $summary = '';
                        foreach ($record->employeeTasks as $employeeTask) {
                            $employeeName = $employeeTask->employee->name ?? 'غير معروف';
                            $rating = $employeeTask->employee_rating;
                            $ratingText = match ((int)$rating) {
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

                TextColumn::make('relatedGoal.goal_text')
                    ->label('الهدف المرتبط')
                    ->searchable()
                    ->sortable()
                    ->words(15)
                    ->toggleable(),

                TextColumn::make('unit.name')
                    ->label('الوحدة')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
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

                Tables\Filters\SelectFilter::make('location')
                    ->label('الموقع')
                    ->options([
                        'قاعة 1 الأسفل' => 'قاعة 1 الأسفل',
                        'قاعة 1 الأعلى' => 'قاعة 1 الأعلى',
                        'قاعة 2 الأسفل' => 'قاعة 2 الأسفل',
                        'قاعة 2 الأعلى' => 'قاعة 2 الأعلى',
                        'قاعة 3 الأسفل' => 'قاعة 3 الأسفل',
                        'قاعة 3 الأعلى' => 'قاعة 3 الأعلى',
                        'قاعة 4 الأسفل' => 'قاعة 4 الأسفل',
                        'قاعة 4 الأعلى' => 'قاعة 4 الأعلى',
                        'قاعة 5 الأسفل' => 'قاعة 5 الأسفل',
                        'قاعة 5 الأعلى' => 'قاعة 5 الأعلى',
                        'قاعة 6 الأسفل' => 'قاعة 6 الأسفل',
                        'قاعة 6 الأعلى' => 'قاعة 6 الأعلى',
                        'قاعة 7 الأسفل' => 'قاعة 7 الأسفل',
                        'قاعة 7 الأعلى' => 'قاعة 7 الأعلى',
                        'قاعة 8 الأسفل' => 'قاعة 8 الأسفل',
                        'قاعة 8 الأعلى' => 'قاعة 8 الأعلى',
                        'قاعة 9 الأسفل' => 'قاعة 9 الأسفل',
                        'قاعة 9 الأعلى' => 'قاعة 9 الأعلى',
                        'قاعة 10 الأسفل' => 'قاعة 10 الأسفل',
                        'قاعة 10 الأعلى' => 'قاعة 10 الأعلى',
                        'قاعة 11 الأسفل' => 'قاعة 11 الأسفل',
                        'قاعة 11 الأعلى' => 'قاعة 11 الأعلى',
                        'قاعة 12 الأسفل' => 'قاعة 12 الأسفل',
                        'قاعة 12 الأعلى' => 'قاعة 12 الأعلى',
                        'قاعة 13 الأسفل' => 'قاعة 13 الأسفل',
                        'قاعة 13 الأعلى' => 'قاعة 13 الأعلى',
                        'جميع القواطع الخارجية' => 'جميع القواطع الخارجية',
                        'الترامز' => 'الترامز',
                        'السجاد' => 'السجاد',
                        'الحاويات' => 'الحاويات',
                        'الجامع' => 'الجامع',
                        'المركز الصحي' => 'المركز الصحي',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('الموظف المنفذ')
                    ->relationship('employeeTasks.employee', 'name')
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
                    ->label('تصدير البيانات'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إنشاء مهمة جديدة'),
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
        // تعيين unit_id لوحدة "النظافة العامة" (ID: 1) تلقائياً
        // تأكد أن الـ ID=1 هو فعلاً لوحدة النظافة العامة في جدول units
        $data['unit_id'] = 1;
        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGeneralCleaningTasks::route('/'),
            'create' => Pages\CreateGeneralCleaningTask::route('/create'),
            'edit' => Pages\EditGeneralCleaningTask::route('/{record}/edit'),
        ];
    }
}
