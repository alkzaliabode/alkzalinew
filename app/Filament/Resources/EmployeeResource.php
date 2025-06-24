<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'الموظفين';
    protected static ?string $navigationGroup = 'إدارة الموظفين'; // المجموعة التي ينتمي إليها المورد
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->label('الاسم'),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->label('البريد الإلكتروني'),

            Forms\Components\TextInput::make('password')
                ->password()
                ->label('كلمة المرور')
                ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                ->required(fn ($context) => $context === 'create')
                ->maxLength(255)
                ->autocomplete('new-password'),

            Forms\Components\TextInput::make('job_title')
                ->label('المسمى الوظيفي'),

            Forms\Components\Select::make('unit_id')
                ->relationship('unit', 'name')
                ->required()
                ->label('الوحدة'),



                Forms\Components\TextInput::make('employee_number')
            ->label('الرقم الوظيفي')
            ->required()
            ->unique(ignoreRecord: true)
            ->maxLength(20)
            ->autofocus()
            ->placeholder('مثال: EMP-001'),


            

            Forms\Components\Select::make('role')
                ->options([
                    'موظف' => 'موظف',
                    'مشرف' => 'مشرف',
                    'مدير' => 'مدير',
                ])
                ->required()
                ->label('الدور'),

            Forms\Components\Toggle::make('is_active')
                ->label('نشط')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable(),
            Tables\Columns\TextColumn::make('employee_number')->label('الرقم الوظيفي'), // تمت الإضافة هنا
            Tables\Columns\TextColumn::make('unit.name')->label('الوحدة'),
            Tables\Columns\TextColumn::make('role')->label('الدور'),
            Tables\Columns\IconColumn::make('is_active')->boolean()->label('نشط'),
            Tables\Columns\TextColumn::make('average_rating')->label('متوسط التقييم')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit_id')
                    ->relationship('unit', 'name')
                    ->label('الوحدة'),
            ])
           ->actions([
    Tables\Actions\EditAction::make(),
])
->bulkActions([
    Tables\Actions\DeleteBulkAction::make(),
    FilamentExportBulkAction::make('export')
        ->label('تصدير البيانات'),
])
->headerActions([
    FilamentExportHeaderAction::make('export')
        ->label('تصدير البيانات'),
]);
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}