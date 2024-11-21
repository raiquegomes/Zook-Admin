<?php

namespace App\Filament\Enterprise\Resources\DepartmentResource\Pages;

use App\Filament\Enterprise\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepartment extends EditRecord
{
    protected static string $resource = DepartmentResource::class;

    public function getTitle(): string
    {
        return 'Editar'; // Título personalizado
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
