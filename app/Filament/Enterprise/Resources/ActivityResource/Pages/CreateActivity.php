<?php

namespace App\Filament\Enterprise\Resources\ActivityResource\Pages;

use App\Filament\Enterprise\Resources\ActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;

    public function getTitle(): string
    {
        return 'Criar uma atividade'; // Título personalizado
    }
}
