<?php

namespace App\Filament\Enterprise\Widgets;

use App\Enums\UserActivityUser;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

use Filament\Tables\Actions\Action;

use Filament\Tables\Enums\FiltersLayout;

use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;

use Filament\Notifications\Notification;

use App\Models\UserActivity;
use App\Models\UserActivityFile;

class AtivityAprovattion extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;
    protected static ?string $heading = 'Registro de Atividades e aprovações';

    public function table(Table $table): Table
    {
        return $table
            ->query(UserActivity::query()) // Use o método `query()` diretamente
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable(),
                Tables\Columns\TextColumn::make('activity.title')
                    ->label('Atividade')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['class' => 'break-words'])
                    ->wrap(), // Habilita quebra automática,
                Tables\Columns\TextColumn::make('assigned_date')
                    ->label('Data Atribuída')
                    ->date('d/m/Y')
                    ->searchable()
                    ->sortable(),
                    Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'concluido' => 'Concluido',
                        'nao_concluido' => 'Não Concluido',
                        'em_analise' => 'Em Analise',
                        'nao_aprovado' => 'Não Aprovado',
                    ])
                    ->searchable(),

            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Action::make('viewImages')
                    ->label('Gerenciar')
                    ->icon('heroicon-s-photo')
                    ->modalWidth('6xl')
                    ->steps([
                        Step::make('Supervisor')
                            ->description('Informações pessoais')
                            ->schema([
                                Placeholder::make('user_id')
                                ->label('O nome do supervisor(a)')
                                ->content(auth()->user()->name),

                                Placeholder::make('id')
                                ->label('Seu ID')
                                ->content(auth()->user()->id),
                            ])
                            ->columns(2),
                        Step::make('Impacto e Reprovação')
                            ->description('Ações Humanizadas')
                            ->schema([
                                Placeholder::make('description')
                                ->label('Informações sobre a atividade')
                                ->content('A reprovação de uma atividade pode gerar consequências diretas para o colaborador,
                                como perda de tempo na correção de tarefas e possível impacto financeiro, reduzindo seu desempenho e remuneração. Antes de reprovar, certifique-se de que todas as orientações foram claras e ofereça sugestões de melhoria. A decisão de reprovar deve ser tomada com cuidado, pois pode afetar a motivação e a eficiência do colaborador a longo prazo.'),
                            ]),
                        Step::make('Informações da Atividade')
                            ->description('Visualização')
                            ->schema([

                                Placeholder::make('activity.title')
                                ->label('Titulo')
                                ->content(fn ($record) => $record->activity->title ?? 'Sem título'),

                                Placeholder::make('observation')
                                ->label('Observação')
                                ->content(fn ($record) => $record->observation ?? 'Sem observação'),

                                FileUpload::make('attachments')
                                ->label('Imagens Enviadas')
                                ->multiple()
                                ->directory('atividades') // Direciona para o diretório correto
                                ->visibility('public') // Garante a acessibilidade
                                ->disabled() // Impede alterações no campo
                                ->preserveFilenames() // Exibe os nomes originais dos arquivos
                                ->default(fn ($record) => $record->attachments ?? [])
                                ->openable()
                                ->panelLayout('grid'),
                            ]),
                    ])
            ]);
    }
}
