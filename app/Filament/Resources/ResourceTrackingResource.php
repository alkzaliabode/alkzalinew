<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourceTrackingResource\Pages;
use App\Filament\Resources\ResourceTrackingResource\RelationManagers;
use App\Models\ResourceTracking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResourceTrackingResource extends Resource
{
    protected static ?string $model = ResourceTracking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'تتبع الموارد';
    protected static ?string $modelLabel = 'تتبع الموارد';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('date')
                ->required()
                ->default(now())
                ->label('التاريخ'),
                
            Forms\Components\Select::make('unit_id')
                ->relationship('unit', 'name')
                ->required()
                ->label('الوحدة')
                ->native(false),
                
            Forms\Components\TextInput::make('working_hours')
                ->numeric()
                ->required()
                ->minValue(0)
                ->maxValue(24)
                ->label('ساعات العمل الإجمالية'),
                
            Forms\Components\TextInput::make('cleaning_materials')
                ->numeric()
                ->required()
                ->minValue(0)
                ->suffix('لتر')
                ->label('مواد التنظيف المستهلكة'),
                
            Forms\Components\TextInput::make('water_consumption')
                ->numeric()
                ->required()
                ->minValue(0)
                ->suffix('لتر')
                ->label('استهلاك المياه'),
                
            Forms\Components\TextInput::make('equipment_usage')
                ->numeric()
                ->required()
                ->minValue(0)
                ->label('عدد المعدات المستخدمة'),
                
            Forms\Components\Textarea::make('notes')
                ->label('ملاحظات')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->label('التاريخ'),
                    
                Tables\Columns\TextColumn::make('unit.name')
                    ->sortable()
                    ->label('الوحدة'),
                    
                Tables\Columns\TextColumn::make('working_hours')
                    ->sortable()
                    ->label('ساعات العمل'),
                    
                Tables\Columns\TextColumn::make('cleaning_materials')
                    ->suffix(' لتر')
                    ->sortable()
                    ->label('مواد التنظيف'),
                    
                Tables\Columns\TextColumn::make('efficiency')
                    ->label('الكفاءة (مهمة/ساعة)')
                    ->state(fn (ResourceTracking $record) => $record->efficiency)
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_efficient')
                    ->label('الكفاءة')
                    ->boolean()
                    ->state(fn (ResourceTracking $record) => $record->efficiency >= 3),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit')
                    ->relationship('unit', 'name')
                    ->label('الوحدة'),
                    
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('to')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('date', '>=', $data['from']))
                            ->when($data['to'], fn ($q) => $q->whereDate('date', '<=', $data['to']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
               'index' => Pages\ListResourceTrackings::route('/'),
        'create' => Pages\CreateResourceTracking::route('/create'),
        'edit' => Pages\EditResourceTracking::route('/{record}/edit'),
        ];
    }
}