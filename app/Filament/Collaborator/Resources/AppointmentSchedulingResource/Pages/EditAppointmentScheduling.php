<?php

namespace App\Filament\Collaborator\Resources\AppointmentSchedulingResource\Pages;

use App\Filament\Collaborator\Resources\AppointmentSchedulingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppointmentScheduling extends EditRecord
{
    protected static string $resource = AppointmentSchedulingResource::class;

    public function getTitle(): string
    {
        return 'Editar um Agendamento'; // Título personalizado
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
