<?php

namespace App\Filament\Resources\SanitationFacilityTaskResource\Pages;

use App\Filament\Resources\SanitationFacilityTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSanitationFacilityTask extends EditRecord
{
    protected static string $resource = SanitationFacilityTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->form->model($this->record)->saveRelationships();
    }
}
