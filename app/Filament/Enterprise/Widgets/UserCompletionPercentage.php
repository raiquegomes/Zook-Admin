<?php

namespace App\Filament\Enterprise\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\ChartWidget;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use App\Models\Performance;
use App\Models\Enterprise;
use App\Models\User;

use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

class UserCompletionPercentage extends ChartWidget
{
    use InteractsWithPageFilters;

    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Visão Geral (%)';

    protected function getData(): array
    {
        // Convertendo os filtros de data (se existirem) em objetos Carbon
        $start = isset($this->filters['startDate']) ? Carbon::parse($this->filters['startDate']) : now()->startOfMonth();
        $end = isset($this->filters['endDate']) ? Carbon::parse($this->filters['endDate']) : now()->endOfMonth();

        $enterprise = Filament::getTenant();

        // Recuperando os usuários da mesma empresa e com departamentos associados
        $users = $enterprise->members()->where('is_active', true)->with('departments')->get(); // Adiciona a relação departments

        $labels = [];
        $averagePercentages = [];

        foreach ($users as $user) {
            // Obtém todos os departamentos do usuário
            $departments = $user->departments;

            // Inicializa a soma das porcentagens de conclusão e o contador
            $totalCompletionPercentage = 0;
            $performanceCount = 0;

            // Itera sobre cada departamento do usuário
            foreach ($departments as $department) {
                // Verifica quantos dias o departamento teve expediente no intervalo de datas selecionado
                $officeDays = $this->calculateOfficeDays($department, $start, $end);

                // Soma das porcentagens de conclusão no intervalo de datas para cada departamento
                $totalCompletionPercentage += Performance::where('user_id', $user->id)
                    ->whereBetween('date', [$start, $end])
                    ->sum('completion_percentage');

                // Número de registros de desempenho no intervalo para cada departamento
                $performanceCount += Performance::where('user_id', $user->id)
                    ->whereBetween('date', [$start, $end])
                    ->count();
            }

            // Cálculo da média: soma das porcentagens dividida pela quantidade de registros
            $averageCompletionPercentage = $performanceCount > 0
                ? $totalCompletionPercentage / $performanceCount
                : 0;

            // Preparando os dados para exibição
            $labels[] = $user->name;
            $averagePercentages[] = round($averageCompletionPercentage, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Média de Conclusão (%)',
                    'data' => $averagePercentages,
                    'backgroundColor' => '#4CAF50',
                    'borderColor' => '#388E3C',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Calcula quantos dias o departamento teve expediente no intervalo de datas.
     */
    protected function calculateOfficeDays($department, Carbon $start, Carbon $end)
    {
        $workDays = $department->work_days;  // Assumindo que isso é um array de dias da semana
        $officeDays = 0;

        // Iterar sobre o intervalo de datas
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (in_array($date->format('l'), $workDays)) {
                $officeDays++;
            }
        }

        return $officeDays;
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
