<?php

namespace App\Filament\Enterprise\Resources\DepartmentResource\Pages;

use App\Filament\Enterprise\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;

    public function getTitle(): string
    {
        return 'Adicionar Departamento'; // Título personalizado
    }
}
