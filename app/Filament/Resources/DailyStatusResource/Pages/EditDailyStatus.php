<?php

namespace App\Filament\Resources\DailyStatusResource\Pages;

use App\Filament\Resources\DailyStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDailyStatus extends EditRecord
{
    protected static string $resource = DailyStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
