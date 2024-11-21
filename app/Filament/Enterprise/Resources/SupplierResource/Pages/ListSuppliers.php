<?php

namespace App\Filament\Enterprise\Resources\SupplierResource\Pages;

use App\Filament\Enterprise\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Facades\Filament;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \EightyNine\ExcelImport\ExcelImportAction::make()
            ->label('Importar CSV')
            ->color("primary")
            ->beforeImport(function (array $data, $livewire, $excelImportAction) {
                $defaultEnterprise = Filament::getTenant();

                // When adding the additional data, the data will be merged with
                // the row data when inserting into the database
                $excelImportAction->additionalData([
                    'enterprise_id' => $defaultEnterprise->id,
                ]);

                // Do some other stuff with the data before importing
            }),
            Actions\CreateAction::make()
            ->label('Criar Fornecedor'),
        ];
    }
}
