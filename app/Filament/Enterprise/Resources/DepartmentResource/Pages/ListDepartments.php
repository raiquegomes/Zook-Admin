<?php

namespace App\Filament\Enterprise\Resources\DepartmentResource\Pages;

use Illuminate\Support\Facades\Auth;

use App\Filament\Enterprise\Resources\DepartmentResource;

use Filament\Forms\Components\Select;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepartments extends ListRecords
{
    protected static string $resource = DepartmentResource::class;

    public function getTitle(): string
    {
        return 'Lista de Departamentos'; // Título personalizado
    }

    protected function getHeaderActions(): array
    {
        return [
            \EightyNine\ExcelImport\ExcelImportAction::make()
            ->slideOver()
            ->label('Importar CSV')
            ->color("primary")
            ->beforeUploadField([
                Select::make('enterprise_id')
                ->relationship('enterprise', 'social_reason')
                ->required()
                ->label('Coorporação'),
            ])
            ->beforeImport(function (array $data, $livewire, $excelImportAction) {
                $defaultEnterprise = $data['enterprise_id'];

                // When adding the additional data, the data will be merged with 
                // the row data when inserting into the database
                $excelImportAction->additionalData([
                    'enterprise_id' => $defaultEnterprise,
                ]);

                // Do some other stuff with the data before importing
            }),
            Actions\CreateAction::make()
            ->label('Criar Departamento'),
        ];
    }
}
