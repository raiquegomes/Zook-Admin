<?php

namespace App\Filament\Collaborator\Widgets;

use Filament\Facades\Filament;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

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
                TextColumn::make('title')->label('Título')->disabled(),
            ])
            ->actions([
                Action::make('completeActivity')
                    ->label('Concluir Atividade')
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
                                TextInput::make('title')
                                ->label('Titulo da Atividade')
                                ->default($record->title),

                                Textarea::make('description')
                                ->label('Informações sobre a atividade')
                                ->default($record->description)
                                ->readonly(),

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

                                FileUpload::make('photos')
                                    ->label('Upload de Imagens')
                                    ->directory('uploads/activities')
                                    ->panelLayout('grid')
                                    ->multiple()
                                    ->image()
                                    ->hidden(fn ($get) => !$get('show_upload'))
                                    ->required(fn ($get) => $get('show_upload')),

                                View::make('components.div-alert'),
                            ];
                        }else{
                            return [
                                TextInput::make('title')
                                ->label('Titulo da Atividade')
                                ->default($record->title)
                                ->readonly(),

                                Textarea::make('description')
                                ->label('Informações sobre a atividade')
                                ->default($record->description)
                                ->rows(5)
                                ->readonly(),

                                Textarea::make('observation')
                                    ->label('Observação'),

                                FileUpload::make('photos')
                                    ->label('Upload de Imagens')
                                    ->directory('uploads/activities')
                                    ->panelLayout('grid')
                                    ->multiple()
                                    ->image(),

                                View::make('components.div-alert'),
                            ];
                        }
                    })
                    ->action(function (Activity $record, array $data) {
                        $userActivity = UserActivity::create([
                            'activity_id' => $record->id,
                            'user_id' => Auth::id(),
                            'status' => 'concluido',
                            'assigned_date' => now(),
                        ]);

                        // Verifique se os caminhos dos arquivos estão disponíveis
                        if (isset($data['photos']) && is_array($data['photos'])) {
                            foreach ($data['photos'] as $path) {
                                UserActivityFile::create([
                                    'path' => $path, // Caminho gerado automaticamente pelo FileUpload
                                    'name' => basename($path), // Extraia o nome do arquivo do caminho
                                    'user_activity_id' => $userActivity->id,
                                ]);
                            }
                        } else {
                            // Adicione uma mensagem de erro para o formulário
                            session()->flash('error', 'Nenhum arquivo foi enviado.');
                        }
                    }),
            ])
            ->heading('Minhas Atividades');
    }

}
