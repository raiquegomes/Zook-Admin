<?php

namespace App\Filament\Enterprise\Resources\EnterpriseResource\Pages;

use App\Filament\Enterprise\Resources\EnterpriseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEnterprise extends EditRecord
{
    protected static string $resource = EnterpriseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
