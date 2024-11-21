<?php

namespace App\Filament\Collaborator\Resources\AssessmentResource\Pages;

use App\Filament\Collaborator\Resources\AssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAssessment extends CreateRecord
{
    protected static string $resource = AssessmentResource::class;

    public function getTitle(): string
    {
        return 'Criar uma Avaliação'; // Título personalizado
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $totalPoints = 0;
        foreach ($data['responses'] as $response) {
            if ($response['answer'] === 'sim') {
                $totalPoints += 10;
            } elseif ($response['answer'] === 'mais_ou_menos') {
                $totalPoints += 5;
            }
        }

        $data['score'] = $totalPoints; // Salva a pontuação final
        return $data;
    }
}
