<?php

namespace App\Filament\Resources\ResourceTrackingResource\Pages;

use App\Filament\Resources\ResourceTrackingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateResourceTracking extends CreateRecord
{
    protected static string $resource = ResourceTrackingResource::class;
}