<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitGoalResource\Pages;
use App\Models\UnitGoal;
use App\Models\DepartmentGoal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UnitGoalResource extends Resource
{
    protected static ?string $model = UnitGoal::class;
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationLabel = 'أهداف الوحدات';
    protected static ?string $navigationGroup = 'الأهداف';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('department_goal_id')
                ->options(
                    DepartmentGoal::with('mainGoal')->get()->mapWithKeys(function ($goal) {
                        return [
                            $goal->id => ($goal->mainGoal ? $goal->mainGoal->goal_text . ' - ' : '') . $goal->goal_text
                        ];
                    })
                )
                ->required()
                ->searchable()
                ->label('هدف القسم (مع الهدف الرئيسي)'),

            Forms\Components\Select::make('unit_id')
                ->relationship('unit', 'name')
                ->label('الوحدة')
                ->searchable(),

            Forms\Components\TextInput::make('unit_name')
                ->required()
                ->label('اسم الوحدة'),

            Forms\Components\Textarea::make('goal_text')
                ->required()
                ->label('نص الهدف')
                ->columnSpanFull(),

            // ✅ إضافة حقل إدخال لـ target_tasks هنا
            Forms\Components\TextInput::make('target_tasks')
                ->label('عدد المهام المستهدفة')
                ->numeric()
                ->required()
                ->minValue(0) // يمكن أن تكون 0 إذا لم يكن هناك هدف كمي محدد، لكن يُفضل وضع قيمة إيجابية
                ->default(1) // قيمة افتراضية لتجنب الصفر إذا نسي المستخدم
                ->helperText('العدد المستهدف من المهام لإنجاز هذا الهدف.')
                ->columnSpanFull(),

            Forms\Components\DatePicker::make('date')
                ->label('التاريخ'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('الوحدة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_name')
                    ->label('اسم الوحدة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('goal_text')
                    ->label('هدف الوحدة')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('departmentGoal.goal_text')
                    ->label('هدف الشعبة')
                    ->limit(40)
                    ->wrap(),
                Tables\Columns\TextColumn::make('departmentGoal.mainGoal.goal_text')
                    ->label('الهدف الرئيسي')
                    ->limit(40)
                    ->wrap(),
                Tables\Columns\TextColumn::make('date')
                    ->label('التاريخ')
                    ->date(),
                // ✅ عرض عمود المهام المستهدفة في الجدول
                Tables\Columns\TextColumn::make('target_tasks')
                    ->label('المستهدف')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('نسبة تحقق الهدف')
                    ->formatStateUsing(fn ($state, $record) => $record->progress_percentage . '%')
                    ->color(fn ($state) => match (true) {
                        $state >= 100 => 'success',
                        $state >= 75 => 'warning',
                        default => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department_goal_id')
                    ->relationship('departmentGoal', 'goal_text')
                    ->label('هدف القسم'),
                Tables\Filters\SelectFilter::make('unit_id')
                    ->relationship('unit', 'name')
                    ->label('الوحدة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnitGoals::route('/'),
            'create' => Pages\CreateUnitGoal::route('/create'),
            'edit' => Pages\EditUnitGoal::route('/{record}/edit'),
        ];
    }
}
