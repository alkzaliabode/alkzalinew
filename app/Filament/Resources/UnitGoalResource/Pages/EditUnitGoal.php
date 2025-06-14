<?php

namespace App\Filament\Resources\UnitGoalResource\Pages;

use App\Filament\Resources\UnitGoalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitGoal extends EditRecord
{
    protected static string $resource = UnitGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
