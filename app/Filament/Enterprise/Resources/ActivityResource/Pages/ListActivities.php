<?php

namespace App\Filament\Enterprise\Resources\ActivityResource\Pages;

use Illuminate\Support\Facades\Auth;

use App\Filament\Enterprise\Resources\ActivityResource;


use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    public function getTitle(): string
    {
        return 'Lista as Atividades'; // Título personalizado
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
            Actions\CreateAction::make()
            ->label('Criar Atividade'),
        ];
    }
}
