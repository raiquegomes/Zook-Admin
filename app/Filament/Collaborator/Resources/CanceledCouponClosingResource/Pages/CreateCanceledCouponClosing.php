<?php

namespace App\Filament\Collaborator\Resources\CanceledCouponClosingResource\Pages;

use App\Filament\Collaborator\Resources\CanceledCouponClosingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCanceledCouponClosing extends CreateRecord
{
    protected static string $resource = CanceledCouponClosingResource::class;

    public function getTitle(): string
    {
        return 'Adicionar um Cancelamento'; // Título personalizado
    }
}
