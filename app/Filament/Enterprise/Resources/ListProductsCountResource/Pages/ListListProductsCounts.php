<?php

namespace App\Filament\Enterprise\Resources\ListProductsCountResource\Pages;

use App\Filament\Enterprise\Resources\ListProductsCountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;

class ListListProductsCounts extends ListRecords
{
    protected static string $resource = ListProductsCountResource::class;

    public function getTitle(): string
    {
        return 'Lista de produtos'; // Título personalizado
    }

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
            Actions\CreateAction::make(),
        ];
    }
}
