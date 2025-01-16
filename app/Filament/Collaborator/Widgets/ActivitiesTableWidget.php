<?php

namespace App\Filament\Collaborator\Widgets;

use Filament\Facades\Filament;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\View;
use Filament\Widgets\TableWidget as BaseWidget;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Activity;
use App\Models\UserActivity;
use App\Models\UserActivityFile;

class ActivitiesTableWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected function getTableQuery(): Builder
    {
        $user = Auth::user();
        $today = now()->toDateString();
        $currentDayName = now()->format('l'); // Nome do dia da semana (e.g., 'Monday')
        $enterprise = Filament::getTenant();
        return Activity::query()
        ->where('enterprise_id', $enterprise->id)
        ->where('status', '!=', 0)
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
        })
        ->whereNotIn('id', function($query) use ($user, $today) {
            $query->select('activity_id')
                ->from('user_activities')
                ->where('user_id', $user->id)
                ->whereDate('assigned_date', $today);
        })
        ->orderBy('order_number', 'asc');
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('order_number')->label('N°')->disabled(),
                TextColumn::make('title')
                ->label('Título')
                ->extraAttributes(['class' => 'break-words'])
                ->wrap(),
            ])
            ->actions([
                Action::make('completeActivity')
                    ->label('Finalizar')
                    ->button()
                    ->icon('heroicon-s-check-circle')
                    ->modalHeading('Completar Atividade')
                    ->modalWidth('lg')
                    ->form(function ($record) {
                        // Carrega as regras se ainda não estiverem carregadas
                        $record->loadMissing('rules');

                        // Verifica se a atividade tem regras vinculadas
                        $hasRules = $record->rules->isNotEmpty();
                        // Configura os campos com base na presença de regras
                        if($hasRules == true){
                            return [
                                Placeholder::make('title')
                                ->label('Título da Atividade')
                                ->content(fn (Activity $record) => $record->title),

                                Placeholder::make('description')
                                ->label('Informações sobre a atividade')
                                ->content(fn (Activity $record) => $record->description),

                                CheckboxList::make('rules')
                                    ->label('Regras da Atividade')
                                    ->options($record->rules->pluck('title', 'id'))
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) use ($record) {
                                        // Atualiza a visibilidade do campo de upload com base nas regras selecionadas
                                        $allChecked = count($state) === $record->rules->count();
                                        $set('show_upload', $allChecked);
                                    }),

                                Textarea::make('observation')
                                    ->label('Observação')
                                    ->hidden(fn ($get) => !$get('show_upload')),

                                FileUpload::make('attachments')
                                    ->directory('atividades')
                                    ->label('Fotos do Cupom')
                                    ->columnSpan('full')
                                    ->multiple()
                                    ->panelLayout('grid')
                                    ->maxFiles(7)
                                    ->openable()
                                    ->visibility('public') // Garanta que os arquivos sejam acessíveis
                                    ->hidden(fn ($get) => !$get('show_upload'))
                                    ->required(fn ($get) => $get('show_upload')),

                                View::make('components.div-alert'),
                            ];
                        }else{
                            return [
                                Placeholder::make('title')
                                ->label('Título da Atividade')
                                ->content(fn (Activity $record) => $record->title),

                                Placeholder::make('description')
                                ->label('Informações sobre a atividade')
                                ->content(fn (Activity $record) => $record->description),

                                Textarea::make('observation')
                                    ->label('Observação'),

                                FileUpload::make('attachments')
                                    ->directory('atividades')
                                    ->label('Fotos da Atividade')
                                    ->columnSpan('full')
                                    ->multiple()
                                    ->panelLayout('grid')
                                    ->openable()
                                    ->maxFiles(7)
                                    ->visibility('public'), // Garanta que os arquivos sejam acessíveis

                                View::make('components.div-alert'),
                            ];
                        }
                    })
                    ->action(function (Activity $record, array $data) {

                        $status = $record->is_monitored ? 'em_analise' : 'concluido';

                        $user = Auth::user();

                        // Obter os departamentos do usuário
                        $departments = $user->departments;

                        // Verificar se algum departamento tem o department_master_id preenchido
                        $departmentMasterId = $departments->firstWhere('department_master_id', '!=', null)?->department_master_id;


                        $userActivity = UserActivity::create([
                            'activity_id' => $record->id,
                            'user_id' => Auth::id(),
                            'status' => $status,
                            'assigned_date' => now(),
                            'attachments' => $data['attachments'] ?? [],
                            'department_master_id' => $departmentMasterId,
                            'observation' => $data['observation'] ?? null,
                        ]);

                        // Verifica se algum arquivo foi enviado
                        if (empty($data['attachments'])) {
                            session()->flash('error', 'Nenhum arquivo foi enviado.');
                        } else {
                            session()->flash('success', 'Atividade concluída com sucesso!');
                        }
                    }),
            ], position: ActionsPosition::BeforeColumns)
            ->heading('Minhas Atividades');
    }

}
