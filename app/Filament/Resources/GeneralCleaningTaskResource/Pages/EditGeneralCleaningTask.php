<?php

namespace App\Filament\Resources\GeneralCleaningTaskResource\Pages;

use App\Filament\Resources\GeneralCleaningTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeneralCleaningTask extends EditRecord
{
    protected static string $resource = GeneralCleaningTaskResource::class;

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
