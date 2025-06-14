<?php

namespace App\Filament\Resources\EmployeeTaskResource\Pages;

use App\Filament\Resources\EmployeeTaskResource;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeTasks extends ListRecords
{
    protected static string $resource = EmployeeTaskResource::class;
}
