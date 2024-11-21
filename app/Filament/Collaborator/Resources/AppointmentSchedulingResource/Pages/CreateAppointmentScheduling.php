<?php

namespace App\Filament\Collaborator\Resources\AppointmentSchedulingResource\Pages;

use App\Filament\Collaborator\Resources\AppointmentSchedulingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAppointmentScheduling extends CreateRecord
{
    protected static string $resource = AppointmentSchedulingResource::class;

    public function getTitle(): string
    {
        return 'Criar um Agendamento'; // Título personalizado
    }
}
