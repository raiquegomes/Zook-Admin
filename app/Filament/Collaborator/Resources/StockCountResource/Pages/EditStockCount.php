<?php

namespace App\Filament\Collaborator\Resources\StockCountResource\Pages;

use App\Filament\Collaborator\Resources\StockCountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockCount extends EditRecord
{
    protected static string $resource = StockCountResource::class;

    public function getTitle(): string
    {
        return 'Atualizar a Contagem'; // Título personalizado
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
