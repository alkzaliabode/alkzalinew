<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyStatusResource\Pages;
use App\Models\DailyStatus;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Filament\Forms\Get; // تأكد من استيراد Get
use Filament\Forms\Set; // تأكد من استيراد Set


class DailyStatusResource extends Resource
{
    protected static ?string $model = DailyStatus::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $modelLabel = 'الموقف اليومي';
    protected static ?string $navigationLabel = 'الموقف اليومي';
    protected static ?string $pluralModelLabel = 'المواقف اليومية'; // التسمية الجمع للموقف اليومي
    protected static ?string $navigationGroup = 'إدارة الموظفين'; // المجموعة التي ينتمي إليها المورد
    protected static ?int $navigationSort = 3; // ترتيب التنقل للمورد
    protected static ?string $recordTitleAttribute = 'date'; // استخدام التاريخ كعنوان السجل
    protected static ?string $slug = 'daily-status'; // المسار الذي يمكن الوصول إليه
    protected static ?string $navigationUrl = 'daily-status'; // URL التنقل للمورد
    protected static ?string $breadcrumb = 'الموقف اليومي'; // التسمية في مسار التنقل
    protected static ?string $breadcrumbGroup = 'إدارة الموظفين'; // المجموعة في مسار التنقل
    protected static ?string $breadcrumbPlural = 'المواقف اليومية'; // التسمية الجمع في مسار التنقل
    protected static ?string $breadcrumbPluralGroup = 'إدارة الموظفين'; // المجموعة في مسار التنقل للجمع

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('معلومات الموقف')
                ->schema([
                    Forms\Components\DatePicker::make('date')
                        ->label('التاريخ')
                        ->required()
                        ->default(now())
                        ->live(), // <--- إضافة هذا السطر لتحديث الحقول الأخرى بشكل مباشر

                    // استخدام Forms\Components\Placeholder لعرض التاريخ الهجري
                    Forms\Components\Placeholder::make('hijri_date')
                        ->label('التاريخ الهجري')
                        // المحتوى يتم حسابه ديناميكيًا بناءً على قيمة حقل 'date'
                        ->content(fn (Get $get) => $get('date') ? self::convertToHijri($get('date')) : null),

                    // استخدام Forms\Components\Placeholder لعرض اليوم
                    Forms\Components\Placeholder::make('day_name')
                        ->label('اليوم')
                        // المحتوى يتم حسابه ديناميكيًا بناءً على قيمة حقل 'date'
                        ->content(fn (Get $get) => $get('date') ? self::getDayName($get('date')) : null),
                ])->columns(3),

            Forms\Components\Section::make('الإجازات')
                ->schema([
                    self::makeLeaveRepeater('periodic_leaves', 'الإجازات الدورية'),
                    self::makeLeaveRepeater('annual_leaves', 'الإجازات السنوية'),
                    self::makeTemporaryLeaveRepeater('temporary_leaves', 'الإجازات الزمنية'),
                ]),

            Forms\Components\Section::make('الغياب والإجازات الأخرى')
                ->schema([
                    self::makeLeaveRepeater('unpaid_leaves', 'إجازة بدون راتب'),
                    self::makeLeaveRepeaterWithDates('absences', 'الغياب'),
                    self::makeLeaveRepeaterWithDates('long_leaves', 'الإجازات الطويلة'),
                    self::makeLeaveRepeaterWithDates('sick_leaves', 'الإجازات المرضية'),
                    self::makeLeaveRepeater('bereavement_leaves', 'إجازة وفاة'),
                ]),

           Forms\Components\Section::make('الإحصائيات')
    ->schema([
        Forms\Components\Placeholder::make('total_required')
            ->label('الملاك  ')
            ->content('86'),

        Forms\Components\Placeholder::make('total_employees')
            ->label('الموجد الحالي')
            ->content(function () {
                return \App\Models\Employee::where('is_active', 1)->count();
            }),

        Forms\Components\Placeholder::make('shortage')
            ->label('النقص ')
            ->content(function () {
                $required = 86;
                $current = \App\Models\Employee::where('is_active', 1)->count();
                return $required - $current;
            }),

        Forms\Components\Placeholder::make('actual_attendance')
            ->label('الحضور الفعلي')
            ->content(function (Get $get) {
                $current = \App\Models\Employee::where('is_active', 1)->count();

                $paidLeaves = count($get('annual_leaves') ?? [])
                            + count($get('periodic_leaves') ?? [])
                            + count($get('sick_leaves') ?? [])
                            + count($get('bereavement_leaves') ?? []);

                $unpaidLeaves = count($get('unpaid_leaves') ?? []);
                $absences = count($get('absences') ?? []);

                return $current - ($paidLeaves + $unpaidLeaves + $absences);
            }),

        Forms\Components\Placeholder::make('paid_leaves_count')
            ->label('إجازات براتب')
            ->content(function (Get $get) {
                return count($get('annual_leaves') ?? [])
                     + count($get('periodic_leaves') ?? [])
                     + count($get('sick_leaves') ?? [])
                     + count($get('bereavement_leaves') ?? []);
            }),

        Forms\Components\Placeholder::make('unpaid_leaves_count')
            ->label('إجازات بدون راتب')
            ->content(fn (Get $get) =>
                count($get('unpaid_leaves') ?? [])
            ),

        Forms\Components\Placeholder::make('absences_count')
            ->label('الغياب')
            ->content(fn (Get $get) =>
                count($get('absences') ?? [])
            ),
    ])
    ->columns(2)
   

        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->label('التاريخ')->date(),
                Tables\Columns\TextColumn::make('hijri_date')->label('التاريخ الهجري'),
                Tables\Columns\TextColumn::make('day_name')->label('اليوم'),
                Tables\Columns\TextColumn::make('total_employees')->label('العدد الكلي'),
                Tables\Columns\TextColumn::make('actual_attendance')->label('الحضور الفعلي'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('طباعة')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('print.daily-status', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export')->label('تصدير البيانات'),
                ]),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')->label('تصدير البيانات'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyStatuses::route('/'),
            'create' => Pages\CreateDailyStatus::route('/create'),
            'edit' => Pages\EditDailyStatus::route('/{record}/edit'),
            'print' => Pages\PrintDailyStatus::route('/{record}/print'),
        ];
    }

    // دوال مساعدة:

    // دالة مساعدة جديدة لإرجاع مخطط المكونات الأساسية للمكرر
    protected static function getBaseLeaveRepeaterSchema(): array
    {
        return [
            Forms\Components\Select::make('employee_id')
                ->label('اسم الموظف')
                ->options(fn () => Employee::pluck('name', 'id'))
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, Set $set) {
                    $employee = Employee::find($state);
                    if ($employee) {
                        $set('employee_number', $employee->employee_number);
                        $set('employee_name', $employee->name);
                    }
                }),
            Forms\Components\TextInput::make('employee_number')->label('الرقم الوظيفي')->numeric()->required(),
            Forms\Components\Hidden::make('employee_name'),
        ];
    }

    // دالة مساعدة جديدة لإرجاع الكول باك الخاص بـ itemLabel
    protected static function getEmployeeItemLabelCallable(): callable
    {
        return fn (array $state): ?string => $state['employee_name'] ?? null;
    }

    protected static function makeLeaveRepeater(string $name, string $label): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make($name)
            ->label($label)
            ->schema(self::getBaseLeaveRepeaterSchema()) // استخدام الدالة المساعدة الجديدة
            ->columns(2)
            ->itemLabel(self::getEmployeeItemLabelCallable()); // استخدام الدالة المساعدة لـ itemLabel
    }

    protected static function makeLeaveRepeaterWithDates(string $name, string $label): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make($name)
            ->label($label)
            ->schema([
                // دمج المكونات الأساسية باستخدام الدالة المساعدة
                ...self::getBaseLeaveRepeaterSchema(), 
                Forms\Components\DatePicker::make('from_date')->label('من')->required(),
                Forms\Components\DatePicker::make('to_date')->label('إلى')->required(),
            ])
            ->columns(4)
            ->itemLabel(self::getEmployeeItemLabelCallable()); // استخدام الدالة المساعدة لـ itemLabel
    }

    protected static function makeTemporaryLeaveRepeater(string $name, string $label): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make($name)
            ->label($label)
            ->schema([
                // دمج المكونات الأساسية باستخدام الدالة المساعدة
                ...self::getBaseLeaveRepeaterSchema(),
                Forms\Components\TimePicker::make('from_time')->label('من الساعة')->required(),
                Forms\Components\TimePicker::make('to_time')->label('إلى الساعة')->required(),
            ])
            ->columns(4)
            ->itemLabel(self::getEmployeeItemLabelCallable()); // استخدام الدالة المساعدة لـ itemLabel
    }

    protected static function convertToHijri(string $gregorianDate): string
    {
        // تغيير التنسيق لعرض اسم الشهر الهجري (F) واليوم (j) والسنة (Y)
        return \Alkoumi\LaravelHijriDate\Hijri::Date('j F Y', $gregorianDate);
    }

    protected static function getDayName(string $date): string
    {
        $days = [
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
            'Saturday' => 'السبت',
        ];

        $day = Carbon::parse($date)->format('l');
        return $days[$day] ?? $day;
    }
}
