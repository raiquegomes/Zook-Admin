<?php

namespace App\Filament\Collaborator\Resources\AssessmentResource\Pages;

use App\Filament\Collaborator\Resources\AssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssessment extends EditRecord
{
    protected static string $resource = AssessmentResource::class;

    public function getTitle(): string
    {
        return 'Editar'; // Título personalizado
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
