<?php

namespace App\Filament\Resources\CleaningResource\Pages;

use App\Filament\Resources\CleaningResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCleaning extends EditRecord
{
    protected static string $resource = CleaningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
