<?php

namespace App\Filament\Collaborator\Resources;

use App\Filament\Collaborator\Resources\AssessmentResource\Pages;
use App\Filament\Collaborator\Resources\AssessmentResource\RelationManagers;
use App\Models\Assessment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationLabel = 'Avaliar';
    protected static ?string $navigationGroup = 'Avaliações';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('repositor_id')
                ->label('Repositor')
                ->relationship('repositor', 'name') // Relaciona com o nome dos repositores
                ->required()
                ->columnSpan('full')
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->label('Nome do Repositor')
                        ->required(),
                ]) // Define o formulário para criar o repositório
                ->createOptionUsing(function (array $data) {
                    // Cria um novo repositório e retorna seu ID
                    $repositor = \App\Models\Repositor::create([
                        'name' => $data['name'], // Captura o nome do repositório a partir do formulário
                    ]);

                    return $repositor->id; // Retorna o ID do repositório criado
                }),
                Forms\Components\Datepicker::make('date')
                ->nullable()
                ->label('Data da Avaliação')
                ->columnSpan('full'),
                Forms\Components\Repeater::make('responses')
                    ->label('Perguntas')
                    ->schema([
                        Forms\Components\TextInput::make('question')
                        ->label('Pergunta')
                        ->readonly(),
                        Forms\Components\Radio::make('answer')
                            ->label('Resposta')
                            ->options([
                                'sim' => 'Sim',
                                'mais_ou_menos' => 'Mais ou menos',
                                'nao' => 'Não',
                            ])
                            ->required(),
                    ]) // Define o número de colunas para exibir as perguntas lado a lado
                    ->default([
                        ['question' => 'Esse colaborador(a) faltou algum dia?'],
                        ['question' => 'Esse colaborador(a) pegou atestado algum dia?'],
                        ['question' => 'Esse colaborador(a) está chegando no horario corretamente?'],
                        ['question' => 'Ele(a) está cumprindo as rotinas corretamente?'],
                        ['question' => 'Segue as orientações do superior?'],
                        ['question' => 'E uma pessoa educada, comunicativa e trabalha em equipe?'],
                        ['question' => 'Ele(a) e uma pessoa proativa?'],
                        ['question' => 'Faz a layoutização correta do setor?'],
                        ['question' => 'A precificação, limpeza e abastecimento está sendo feito?'],
                        ['question' => 'A gestão de validade e rupturas estão sendo feitos?'],
                        ['question' => 'Faz o atendimento padrão?'],
                        ['question' => 'Está aplicando o layout inteligente (Cross, Pontos extras criativos etc)?'],
                    ])
                    ->minItems(1)
                    ->columns(3) // Número de colunas para o layout horizontal
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('repositor.name')->label('Repositor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')->label('Data da Avaliação')
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')->label('Pontuação')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssessments::route('/'),
            'create' => Pages\CreateAssessment::route('/create'),
            'edit' => Pages\EditAssessment::route('/{record}/edit'),
        ];
    }
}
