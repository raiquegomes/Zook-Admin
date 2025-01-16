<?php

namespace App\Filament\Collaborator\Resources\StockCountResource\Pages;

use App\Filament\Collaborator\Resources\StockCountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockCounts extends ListRecords
{
    protected static string $resource = StockCountResource::class;

    public function getTitle(): string
    {
        return 'Contagem'; // Título personalizado
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
