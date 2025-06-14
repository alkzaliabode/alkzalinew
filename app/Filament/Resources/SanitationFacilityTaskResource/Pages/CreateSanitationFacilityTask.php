<?php

namespace App\Filament\Resources\SanitationFacilityTaskResource\Pages;

use App\Filament\Resources\SanitationFacilityTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSanitationFacilityTask extends CreateRecord
{
    protected static string $resource = SanitationFacilityTaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();
        return $data;
    }

    /**
     * هذا يحفظ العلاقة employeeTasks تلقائياً من Repeater.
     */
    protected function afterCreate(): void
    {
        $this->form->model($this->record)->saveRelationships();
    }
}