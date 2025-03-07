<?php

namespace App\Filament\Enterprise\Resources\EnterpriseResource\Pages;

use App\Filament\Enterprise\Resources\EnterpriseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEnterprises extends ListRecords
{
    protected static string $resource = EnterpriseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
