<?php

namespace App\Filament\Resources\TaskImageReportResource\Pages;

use App\Filament\Resources\TaskImageReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaskImageReport extends EditRecord
{
    protected static string $resource = TaskImageReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
