<?php

namespace App\Filament\Collaborator\Resources\AssessmentResource\Pages;

use App\Filament\Collaborator\Resources\AssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssessments extends ListRecords
{
    protected static string $resource = AssessmentResource::class;

    public function getTitle(): string
    {
        return 'Avaliações'; // Título personalizado
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
