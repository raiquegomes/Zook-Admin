<?php

namespace App\Filament\Collaborator\Widgets;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Activity;
use Carbon\Carbon;

class ProgressDayCustom extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $today = Carbon::today();
        $currentDayName = $today->format('l'); // Nome do dia atual
        $enterprise = Filament::getTenant();

        // Consulta para obter as atividades do dia para o usuário
        $activitiesQuery = Activity::query()
            ->where('enterprise_id', $enterprise->id)
            ->where('status', '=', 1)
            ->whereHas('departments', function($query) use ($user) {
                $query->whereIn('departments.id', $user->departments()->pluck('departments.id'));
            })
            ->where(function($query) use ($currentDayName) {
                $query->where('frequency', 'daily')
                      ->orWhere(function($query) use ($currentDayName) {
                          $query->where('frequency', 'specific_day')
                                ->whereJsonContains('specific_days', $currentDayName);
                      });
                // Adicionar condições para outras frequências como '5th_working_day', 'weekly', 'monthly' se necessário
            });

        // Contando o total de atividades que o usuário tem que fazer hoje
        $totalActivities = (clone $activitiesQuery)->count();

        // Contando as atividades concluídas
        $completedActivities = (clone $activitiesQuery)
            ->whereHas('userActivities', function($query) use ($user, $today) {
                $query->where('user_id', $user->id)
                      ->where('status', 'concluido')
                      ->whereDate('created_at', $today);
            })
            ->count();

        // Calculando a porcentagem de atividades concluídas
        $completionPercentage = $totalActivities > 0
            ? ($completedActivities / $totalActivities) * 100
            : 0;

        return [
            Stat::make('Progresso do Dia', round($completionPercentage) . '%')
                ->description("Atividades concluídas hoje")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color($completionPercentage == 100 ? 'success' : 'primary'),
        ];
    }

}
