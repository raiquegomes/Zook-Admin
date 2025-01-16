<?php

namespace App\Filament\Collaborator\Resources\CanceledCouponClosingResource\Pages;

use App\Filament\Collaborator\Resources\CanceledCouponClosingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCanceledCouponClosings extends ListRecords
{
    protected static string $resource = CanceledCouponClosingResource::class;

    public function getTitle(): string
    {
        return 'Lista de Cancelamento'; // Título personalizado
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Criar'),
        ];
    }
}
