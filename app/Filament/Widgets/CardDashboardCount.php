<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Collaborator;
use App\Models\Enterprise;

class CardDashboardCount extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEnterprise = Enterprise::count();
        $enterpriseActive = Enterprise::where('is_active', true)->count();
        $enterpriseInative = Enterprise::where('is_active', false)->count();

        return [
            Stat::make('Total de Corporações', $totalEnterprise)
                ->description('Número total de coraporações registradas')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->icon('heroicon-s-building-office-2'),

            Stat::make('Ativas', $enterpriseActive)
                ->description('Número de corporações atualmente ativos')
                ->icon('heroicon-s-check-circle')
                ->color('success'),

            Stat::make('Inativos', $enterpriseInative)
                ->description('Número de corporações inativos')
                ->icon('heroicon-s-x-circle')
                ->color('danger'),
        ];
    }
}
