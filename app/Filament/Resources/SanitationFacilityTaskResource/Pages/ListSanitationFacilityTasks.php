<?php

namespace App\Filament\Resources\SanitationFacilityTaskResource\Pages;

use App\Filament\Resources\SanitationFacilityTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSanitationFacilityTasks extends ListRecords
{
    protected static string $resource = SanitationFacilityTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
