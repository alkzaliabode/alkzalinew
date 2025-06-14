<?php

namespace App\Filament\Resources\GeneralCleaningTaskResource\Pages;

use App\Filament\Resources\GeneralCleaningTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeneralCleaningTasks extends ListRecords
{
    protected static string $resource = GeneralCleaningTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
