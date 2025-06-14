<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SanitationFacilityTaskResource\Pages;
use App\Models\SanitationFacilityTask;
use App\Models\Employee;
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
                                    ->label('الوجبة')
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
                            ]),
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
                                    ->live(),
                                    
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
                                     TextInput::make('details')
                                     ->label('تفاصيل إضافية')
                                     ->required()
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
                                            ->numeric()
                                            ->minValue(0)
                                            ->label("{$prefix} المقاعد {$suffix}")
                                            ->columnSpan(1),
                                            
                                        TextInput::make('mirrors_count')
                                            ->numeric()
                                            ->minValue(0)
                                            ->label("{$prefix} المرايا {$suffix}")
                                            ->columnSpan(1),
                                            
                                        TextInput::make('mixers_count')
                                            ->numeric()
                                            ->minValue(0)
                                            ->label("{$prefix} الخلاطات {$suffix}")
                                            ->columnSpan(1),
                                            
                                        TextInput::make('doors_count')
                                            ->numeric()
                                            ->minValue(0)
                                            ->label("{$prefix} الأبواب {$suffix}")
                                            ->columnSpan(1),
                                            
                                        TextInput::make('sinks_count')
                                            ->numeric()
                                            ->minValue(0)
                                            ->label("{$prefix} المغاسل {$suffix}")
                                            ->columnSpan(1),
                                            
                                            TextInput::make('toilets_count')
                                             ->numeric()
                                             ->minValue(0)
                                              ->label("{$prefix} الحمامات {$suffix}")
                                               ->columnSpan(1),
                                    ]);
                                    
                                return $fields;
                            })
    ->hidden(fn ($get) => empty($get('task_type'))),
                    ]),
                    
                Section::make('الموارد المستخدمة')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('resources_used')
                            ->label('')
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
                                    
                                TextInput::make('working_hours')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(24)
                                    ->label('ساعات العمل')
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->createItemButtonLabel('إضافة مورد جديد')
                            ->defaultItems(1),
                    ]),
                    
                Section::make('المنفذون والتقييم')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('employeeTasks')
                            ->label('')
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
                            ->createItemButtonLabel('إضافة منفذ جديد')
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

    public static function table(Table $table): Table
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
                         default => 'gray', // ← هذا يضمن تجنب الخطأ إن جاءت قيمة غير متوقعة

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
                    
                Tables\Columns\TextColumn::make('employeeTasks.employee.name')
                    ->label('المنفذون')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList(),
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
                    ->label('الوجبة')
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
                FilamentExportBulkAction::make('export')
                    ->label('تصدير البيانات'),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('تصدير البيانات'),
            ]);
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