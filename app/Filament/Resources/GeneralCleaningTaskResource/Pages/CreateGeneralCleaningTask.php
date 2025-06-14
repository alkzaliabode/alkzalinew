<?php

namespace App\Filament\Resources\GeneralCleaningTaskResource\Pages;

use App\Filament\Resources\GeneralCleaningTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGeneralCleaningTask extends CreateRecord
{
    protected static string $resource = GeneralCleaningTaskResource::class;

    /**
     * إذا كنت ترغب بإضافة user_id للمهام الجديدة، أبقه هنا.
     */
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
