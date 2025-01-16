<?php

namespace App\Filament\Enterprise\Resources\ListProductsCountResource\Pages;

use App\Filament\Enterprise\Resources\ListProductsCountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditListProductsCount extends EditRecord
{
    protected static string $resource = ListProductsCountResource::class;

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
