<?php

namespace App\Filament\Enterprise\Resources\ActivityResource\Pages;

use App\Filament\Enterprise\Resources\ActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivity extends EditRecord
{
    protected static string $resource = ActivityResource::class;

    public function getTitle(): string
    {
        return 'Editar Atividade'; // Título personalizado
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
