<?php

namespace App\Filament\Collaborator\Widgets;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Support\Enums\IconPosition;
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
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $workDays = $user->departments()->pluck('work_days')->flatten();

        // --- Progresso do Dia ---
        $dailyActivitiesQuery = Activity::query()
            ->where('enterprise_id', $enterprise->id)
            ->where('status', '=', 1)
            ->whereHas('departments', function ($query) use ($user) {
                $query->whereIn('departments.id', $user->departments()->pluck('departments.id'));
            })
            ->where(function ($query) use ($currentDayName) {
                $query->where('frequency', 'daily')
                      ->orWhere(function ($query) use ($currentDayName) {
                          $query->where('frequency', 'specific_day')
                                ->whereJsonContains('specific_days', $currentDayName);
                      });
            });

        $totalActivitiesToday = $dailyActivitiesQuery->count();
        $completedActivitiesToday = $dailyActivitiesQuery
            ->whereHas('userActivities', function ($query) use ($user, $today) {
                $query->where('user_id', $user->id)
                      ->whereDate('assigned_date', $today);
            })
            ->count();

        $completionPercentageToday = $totalActivitiesToday > 0
            ? ($completedActivitiesToday / $totalActivitiesToday) * 100
            : 0;

        // --- Média Mensal ---
        $totalScheduledActivities = 0;
        $totalCompletedActivities = 0;

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            if (in_array($date->format('l'), $workDays->toArray())) {
                $dayActivitiesQuery = Activity::query()
                    ->where('enterprise_id', $enterprise->id)
                    ->where('status', '=', 1)
                    ->whereHas('departments', function ($query) use ($user) {
                        $query->whereIn('departments.id', $user->departments()->pluck('departments.id'));
                    })
                    ->where(function ($query) use ($date) {
                        $currentDayName = $date->format('l');
                        $query->where('frequency', 'daily')
                              ->orWhere(function ($query) use ($currentDayName) {
                                  $query->where('frequency', 'specific_day')
                                        ->whereJsonContains('specific_days', $currentDayName);
                              });
                    });

                $dailyTotalActivities = $dayActivitiesQuery->count();
                $dailyCompletedActivities = $dayActivitiesQuery
                    ->whereHas('userActivities', function ($query) use ($user, $date) {
                        $query->where('user_id', $user->id)
                              ->whereDate('assigned_date', $date);
                    })
                    ->count();

                $totalScheduledActivities += $dailyTotalActivities;
                $totalCompletedActivities += $dailyCompletedActivities;
            }
        }

        $monthlyCompletionPercentage = $totalScheduledActivities > 0
            ? ($totalCompletedActivities / $totalScheduledActivities) * 100
            : 0;

        return [
            Stat::make('Progresso do Dia', round($completionPercentageToday) . '%')
                ->description('Atividades concluídas hoje')
                ->descriptionIcon('heroicon-o-check-circle', IconPosition::Before)
                ->color($completionPercentageToday == 100 ? 'success' : 'primary'),

            Stat::make('Média Mensal', round($monthlyCompletionPercentage) . '%')
                ->description('Atividades concluídas no mês')
                ->descriptionIcon('heroicon-o-calendar')
                ->color($monthlyCompletionPercentage == 100 ? 'success' : 'primary'),
        ];
    }
}
