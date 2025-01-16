<?php

namespace App\Filament\Enterprise\Resources\ListProductsCountResource\Pages;

use App\Filament\Enterprise\Resources\ListProductsCountResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateListProductsCount extends CreateRecord
{
    protected static string $resource = ListProductsCountResource::class;

    public function getTitle(): string
    {
        return 'Adicionar produto na listagem'; // Título personalizado
    }
}
