<?php

namespace App\Filament\Enterprise\Widgets;

use Filament\Widgets\Widget;
use App\Models\Performance;
use App\Models\User;
use App\Models\Enterprise;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;
use Carbon\Carbon;

class FunctionsUserActivity extends Widget
{
    protected static string $view = 'filament.enterprise.widgets.functions-user-activity';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public $startDate;
    public $endDate;
    public $usuarios = [];

    public function mount() // Método que é chamado na inicialização do widget
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function submit()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        $enterprise = Filament::getTenant();

        // Recupera os usuários ativos vinculados à empresa
        $users = $enterprise->members()
            ->where('is_active', true)
            ->with('departments')
            ->get();

        $this->usuarios = $users->map(function ($user) use ($start, $end) {
            $departments = $user->departments;

            $totalCompletionPercentage = 0;
            $performanceCount = 0;

            foreach ($departments as $department) {
                $officeDays = $this->calculateOfficeDays($department, $start, $end);

                $totalCompletionPercentage += Performance::where('user_id', $user->id)
                    ->whereBetween('date', [$start, $end])
                    ->sum('completion_percentage');

                $performanceCount += Performance::where('user_id', $user->id)
                    ->whereBetween('date', [$start, $end])
                    ->count();
            }

            $averageCompletionPercentage = $performanceCount > 0
                ? $totalCompletionPercentage / $performanceCount
                : 0;

            return [
                'name' => $user->name,
                'average_percentage' => round($averageCompletionPercentage, 2),
            ];
        })->toArray();
    }

    /**
     * Calcula quantos dias o departamento teve expediente no intervalo de datas.
     */
    protected function calculateOfficeDays($department, Carbon $start, Carbon $end)
    {
        $workDays = $department->work_days;
        $officeDays = 0;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if (in_array($date->format('l'), $workDays)) {
                $officeDays++;
            }
        }

        return $officeDays;
    }


}
