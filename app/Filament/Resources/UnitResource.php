<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Filament\Resources\UnitResource\RelationManagers;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $modelLabel = 'وحدة';
    protected static ?string $pluralModelLabel = 'الوحدات';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->label('اسم الوحدة'),
                
            Forms\Components\TextInput::make('code')
                ->required()
                ->maxLength(10)
                ->unique(ignoreRecord: true)
                ->label('كود الوحدة'),
                
            Forms\Components\Textarea::make('description')
                ->label('وصف الوحدة')
                ->columnSpanFull(),
                
            Forms\Components\Toggle::make('is_active')
                ->label('الحالة نشطة')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('الاسم'),
                    
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->label('الكود'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشطة')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('النشطة فقط')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}