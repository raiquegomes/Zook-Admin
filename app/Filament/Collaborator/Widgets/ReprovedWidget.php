<?php

namespace App\Filament\Collaborator\Widgets;

use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\UserActivity;
use Filament\Widgets\TableWidget as BaseWidget;

class ReprovedWidget extends BaseWidget
{
    protected static ?int $sort = 4; // Define a ordem de exibição do widget
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        $user = Auth::user();
        $enterprise = Filament::getTenant();

        return UserActivity::query()
            ->where('user_id', $user->id) // Atividades relacionadas ao usuário autenticado
            ->where('status', 'nao_aprovado') // Filtra apenas atividades reprovadas
            ->whereHas('activity', function ($query) use ($enterprise) {
                $query->where('enterprise_id', $enterprise->id); // Garante que a atividade pertence à empresa do usuário
            })
            ->orderBy('assigned_date', 'desc'); // Ordena por data de atribuição
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('activity.title')
                    ->label('Título da Atividade')
                    ->searchable()
                    ->extraAttributes(['class' => 'break-words'])
                    ->wrap(),
                TextColumn::make('assigned_date')
                    ->label('Data de Atribuição')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')->label('Status')->limit(50),
            ])
            ->actions([])
            ->heading('Atividades Reprovadas');
    }
}
