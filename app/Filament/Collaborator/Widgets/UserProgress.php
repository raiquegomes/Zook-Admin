<?php

namespace App\Filament\Collaborator\Widgets;

use Illuminate\Support\Facades\Auth;

use Filament\Widgets\ChartWidget;

use App\Models\Performance;

use Carbon\Carbon;

class UserProgress extends ChartWidget
{
    protected static ?string $heading = 'Progresso do Usuário (Últimos 30 Dias)';

    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $userId = Auth::id();
        $currentMonth = Carbon::now()->format('Y-m');
        $performanceData = Performance::where('user_id', $userId)
            ->where('date', 'like', "$currentMonth%")
            ->orderBy('date', 'asc')
            ->get();
        $labels = [];
        $concludedPercentages = [];

        foreach ($performanceData as $performance) {
            $labels[] = Carbon::parse($performance->date)->format('d/m');
            $concludedPercentages[] = $performance->completion_percentage;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Atividades Concluídas (%)',
                    'data' => $concludedPercentages,
                    'backgroundColor' => '#4CAF50',
                    'borderColor' => '#388E3C',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
