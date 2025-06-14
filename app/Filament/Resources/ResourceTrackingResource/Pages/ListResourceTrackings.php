<?php

namespace App\Filament\Resources\ResourceTrackingResource\Pages;

use App\Filament\Resources\ResourceTrackingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResourceTrackings extends ListRecords
{
    protected static string $resource = ResourceTrackingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}