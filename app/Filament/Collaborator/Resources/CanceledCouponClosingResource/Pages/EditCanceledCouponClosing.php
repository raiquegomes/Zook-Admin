<?php

namespace App\Filament\Collaborator\Resources\CanceledCouponClosingResource\Pages;

use App\Filament\Collaborator\Resources\CanceledCouponClosingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCanceledCouponClosing extends EditRecord
{
    protected static string $resource = CanceledCouponClosingResource::class;

    public function getTitle(): string
    {
        return 'Editar Cancelamento'; // Título personalizado
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
