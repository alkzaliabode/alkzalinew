<?php

namespace App\Filament\Resources\DailyStatusResource\Pages;

use App\Filament\Resources\DailyStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDailyStatuses extends ListRecords
{
    protected static string $resource = DailyStatusResource::class;

    public static function getRoutes(): array
{
    return [
        'index' => Pages\ListDailyStatuses::route('/'),
        'create' => Pages\CreateDailyStatus::route('/create'),
        'edit' => Pages\EditDailyStatus::route('/{record}/edit'),
        'print' => Pages\PrintDailyStatus::route('/{record}/print'),
    ];
}

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
