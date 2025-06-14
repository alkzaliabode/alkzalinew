<?php

namespace App\Filament\Resources;

use App\Models\ActualResult;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use App\Filament\Resources\ActualResultResource\Pages;

class ActualResultResource extends Resource
{
    protected static ?string $model = ActualResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'النتائج'; // Navigation label in Arabic
    protected static ?string $navigationGroup = 'إدارة الأداء'; // Navigation group in Arabic
    protected static ?int $navigationSort = 2; // Adjust the sort order as needed
    protected static ?string $recordTitleAttribute = 'date'; // Use date as the record title

    public static function form(Forms\Form $form): Forms\Form
{
    return $form->schema([
        Forms\Components\DatePicker::make('date')
            ->disabled()
            ->label('التاريخ'),

        Forms\Components\Select::make('unit_id')
            ->relationship('unit', 'name')
            ->disabled()
            ->label('الوحدة'),

        Forms\Components\TextInput::make('completed_tasks')
            ->disabled()
            ->label('المهام المكتملة'),

        Forms\Components\Select::make('quality_rating')
            ->options([
                1 => '⭐',
                2 => '⭐⭐',
                3 => '⭐⭐⭐',
                4 => '⭐⭐⭐⭐',
                5 => '⭐⭐⭐⭐⭐',
            ])
            ->disabled()
            ->label('تقييم الجودة'),

        Forms\Components\TextInput::make('efficiency_score')
            ->disabled()
            ->label('درجة الكفاءة (1-100)'),
    ]);
}

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('date')
                ->date()
                ->label('التاريخ')
                ->sortable(),

            Tables\Columns\TextColumn::make('unit.name')
                ->label('الوحدة'),

            Tables\Columns\TextColumn::make('completed_tasks')
                ->label('المكتمل')
                ->sortable(),

            Tables\Columns\TextColumn::make('completion_rate')
                ->label('% الإنجاز')
                ->suffix('%')
                ->color(fn ($record) => match (true) {
                    $record->completion_rate >= 90 => 'success',
                    $record->completion_rate >= 70 => 'warning',
                    default => 'danger',
                }),

            Tables\Columns\IconColumn::make('quality_rating')
                ->label('الجودة')
                ->icon('heroicon-o-star')
                ->color('warning'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActualResults::route('/'),
            'create' => Pages\CreateActualResult::route('/create'),
            'edit' => Pages\EditActualResult::route('/{record}/edit'),
        ];
    }
}
