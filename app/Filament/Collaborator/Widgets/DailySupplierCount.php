<?php

namespace App\Filament\Collaborator\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use App\Models\AppointmentScheduling;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\View;

class DailySupplierCount extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user->departments->contains(fn($department) => $department->show_supplier_count);
    }

    protected function getTableQuery(): Builder
    {
        // Filtrar os agendamentos do dia atual e pelos departamentos vinculados ao usuário logado
        $today = Carbon::today();
        $userDepartments = Auth::user()->departments->pluck('id');

        return AppointmentScheduling::whereHas('departments', function (Builder $query) use ($userDepartments) {
                $query->whereIn('departments.id', $userDepartments);
            })
            ->whereDoesntHave('users', function (Builder $query) {
                $query->where('user_id', Auth::id())->whereNotNull('completed_at');
            })
            ->with('supplier');
    }


    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('supplier.name')
                ->label('Fornecedor')
                ->sortable()
                ->searchable()
                ->extraAttributes(['class' => 'break-words'])
                ->wrap(), // Habilita quebra automática,
            Tables\Columns\TextColumn::make('scheduled_date')
                ->label('Data do Agendamento')
                ->date()
                ->extraAttributes(['class' => 'break-words'])
                ->wrap() // Habilita quebra automática
                ->sortable(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())  // Usar a query personalizada
            ->columns($this->getTableColumns())  // Definir as colunas
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Confirmar')
                    ->icon('heroicon-s-check-circle')
                    ->modalHeading('Agendamento')
                    ->modalWidth('lg')
                    ->form(function ($record) {
                        // Carrega as regras se ainda não estiverem carregadas
                            return [
                                TextInput::make('balance_id')
                                ->numeric()
                                ->required()
                                ->label('N° do Balanço'),

                                View::make('components.div-alert'),
                            ];
                        })
                    ->action(function (AppointmentScheduling $record, array $data) {

                        // Relaciona o usuário logado com o agendamento e marca a conclusão
                        $record->users()->attach(Auth::id(), [
                            'balance_id' => $data['balance_id'],
                            'completed_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check'),
                ], position: ActionsPosition::BeforeColumns)
            ->heading('Fornecedores para Contagem do Dia');
    }
}
