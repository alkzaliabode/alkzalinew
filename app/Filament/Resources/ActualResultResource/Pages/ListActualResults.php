<?php

namespace App\Filament\Resources\ActualResultResource\Pages;

use App\Filament\Resources\ActualResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActualResults extends ListRecords
{
    protected static string $resource = ActualResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
