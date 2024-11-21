<?php

namespace App\Filament\Enterprise\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Enterprise;

use Filament\Facades\Filament;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Obtém a empresa do tenant atual
        $enterprise = Filament::getTenant(); // Ajuste isso de acordo com sua lógica de multi-tenancy
        // Contagem de colaboradores
        $totalColaboradores = $enterprise->members()->count();
        $totalActivity = $enterprise->activities()->count();
        // Contagem de colaboradores ativos
        $colaboradoresAtivos = $enterprise->members()->where('is_active', true)->count();
        // Contagem de colaboradores inativos
        $colaboradoresInativos = $totalColaboradores - $colaboradoresAtivos;

        return [
            Stat::make('Colaboradores', $totalColaboradores)
            ->description('Total de colaboradores')
            ->color('success')
            ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make('Atividades', $totalActivity)
            ->description('Total de atividades cadastradas')
            ->color('primary'),

            Stat::make('Colaboradores Inativos', $colaboradoresInativos)
            ->description('Todos os colaboradores inativos')
            ->descriptionIcon('heroicon-m-arrow-trending-down')
            ->color('danger'),
        ];
    }
}
