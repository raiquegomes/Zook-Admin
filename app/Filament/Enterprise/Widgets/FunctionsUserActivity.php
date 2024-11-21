<?php

namespace App\Filament\Enterprise\Widgets;

use Filament\Widgets\Widget;
use App\Models\User;

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

        // Obtenha os usuários e some as porcentagens do modelo Performance
        $this->usuarios = User::with(['performances' => function ($query) {
            $query->whereBetween('date', [$this->startDate, $this->endDate]); // Ajuste o campo de data conforme necessário
        }])->get()->map(function ($user) {
            $totalPorcentagem = $user->performances->sum('completion_percentage'); // Substitua 'percentage' pelo nome correto do campo no modelo Performance

            return [
                'name' => $user->name,
                'total_percentage' => $totalPorcentagem,
            ];
        })->toArray();
    }

}
