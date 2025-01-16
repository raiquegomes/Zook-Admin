<?php

namespace App\Filament\Enterprise\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use App\Console\Commands\CheckAndCalculateDailyPerformance;

use App\Models\Performance;

class ReprocessActivityPorcent extends Widget
{
    protected static string $view = 'filament.enterprise.widgets.reprocess-activity-porcent';

    protected int | string | array $columnSpan = 'full';

    public $date;

    public function reprocessActivities()
    {
        if (!$this->date) {
            Notification::make()
            ->title('ATENÇÃO!')
            ->danger()
            ->body('Por favor, selecione uma data!')
            ->send();
            return;
        }

        // Converte a data selecionada para um objeto Carbon
        $date = Carbon::parse($this->date)->startOfDay();

        // Verifica se a data selecionada é o dia atual
        if ($date->isToday()) {
            Notification::make()
                ->title('ATENÇÃO!')
                ->danger()
                ->body('Você não pode processa as atividade da data atual, so pode ser processada data de dias que não foram processados!')
                ->send();
            return;
        }

        // Verifica se já existe performance calculada para essa data
        $performanceExists = Performance::whereDate('date', $this->date)->exists();

        if ($performanceExists) {
            Notification::make()
                ->title('ATENÇÃO!')
                ->warning()
                ->body('As atividades para o dia ' . $this->date . ' já foram processadas!')
                ->send();
            return;
        }


        // Atualiza todas as atividades "em análise" para "concluídas" da data selecionada
        $updated = \App\Models\UserActivity::where('status', 'em_analise')
            ->whereDate('assigned_date', $date)
            ->update(['status' => 'concluido']);

        // Instancia o comando e executa a lógica
        $command = new CheckAndCalculateDailyPerformance();
        $command->handle($date);


        Notification::make()
            ->title('Sucesso!')
            ->success()
            ->body('O dia'.$this->date.' foi processado com sucesso!')
            ->send();
    }
}
