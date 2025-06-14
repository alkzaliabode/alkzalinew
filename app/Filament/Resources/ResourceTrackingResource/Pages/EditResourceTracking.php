<?php

namespace App\Filament\Resources\ResourceTrackingResource\Pages;

use App\Filament\Resources\ResourceTrackingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResourceTracking extends EditRecord
{
    protected static string $resource = ResourceTrackingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}