<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeTaskResource\Pages;
use App\Models\EmployeeTask;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeTaskResource extends Resource
{
    protected static ?string $model = EmployeeTask::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $recordTitleAttribute = 'employee.name'; // Use employee name as the record title
    protected static ?string $slug = 'employee-tasks';
    protected static ?string $pluralModelLabel = 'تقييمات المنفذين';
    protected static ?string $modelLabel = 'تقييم منفذ';
    protected static ?string $modelLabelPlural = 'تقييمات المنفذين';
    protected static ?string $navigationGroup = 'إدارة الأداء'; // Navigation group in Arabic
    protected static ?int $navigationSort = 3; // Adjust the sort order as needed

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeTasks::route('/'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')->label('اسم الموظف'),
                Tables\Columns\TextColumn::make('task_type_label')->label('نوع المهمة'),
                Tables\Columns\TextColumn::make('location_label')->label('الموقع / المرفق الصحي'),
                Tables\Columns\TextColumn::make('date_label')->label('التاريخ'),
                Tables\Columns\TextColumn::make('employee_rating')->label('التقييم')->sortable(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]); // غير مطلوب حالياً
    }
}