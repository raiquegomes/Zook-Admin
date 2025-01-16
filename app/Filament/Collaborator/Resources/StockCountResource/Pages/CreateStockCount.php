<?php

namespace App\Filament\Collaborator\Resources\StockCountResource\Pages;

use App\Filament\Collaborator\Resources\StockCountResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStockCount extends CreateRecord
{
    protected static string $resource = StockCountResource::class;

    public function getTitle(): string
    {
        return 'Criar Contagem'; // Título personalizado
    }

}
