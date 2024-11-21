<?php

namespace App\Filament\Collaborator\Resources\AppointmentSchedulingResource\Pages;

use App\Filament\Collaborator\Resources\AppointmentSchedulingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppointmentSchedulings extends ListRecords
{
    protected static string $resource = AppointmentSchedulingResource::class;

    public function getTitle(): string
    {
        return 'Agendamentos'; // Título personalizado
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Agendar'),
        ];
    }
}
