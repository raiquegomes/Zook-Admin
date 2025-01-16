<?php

namespace App\Filament\Enterprise\Resources;

use App\Filament\Enterprise\Resources\ActivityResource\Pages;
use App\Filament\Enterprise\Resources\ActivityResource\RelationManagers;

use App\Models\Activity;
use App\Models\Department;

use App\Enums\ActivityStatus;
use App\Filament\Exports\ActivityExporter;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;

use Filament\Resources\Resource;

use Filament\Forms\Get;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationLabel = 'Atividades';
    protected static ?string $navigationGroup = 'Gerenciamento';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('title')
                ->required()
                ->label('Título'),
            Forms\Components\Textarea::make('description')
                ->nullable()
                ->label('Descrição'),
            Forms\Components\Select::make('frequency')
                ->label('Frequência')
                ->options([
                    'daily' => 'Diária',
                    'specific_day' => 'Dia específico',
                    '5th_working_day' => '5º Dia Útil',
                    'weekly' => 'Semanalmente',
                    'monthly' => 'Mensal',
                ])->required(),
            Forms\Components\TextInput::make('specific_day')
                ->label('Dia Específico')
                ->nullable()
                ->visible(fn (Get $get): bool => $get('frequency') === 'specific_day'),
            Forms\Components\BelongsToManyMultiSelect::make('departments')
                ->relationship(
                    name: 'departments',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()),
                )
                ->label('Departamento'),
            Forms\Components\Repeater::make('rules')
                ->label('Regras')
                ->relationship('rules')
                ->schema([
                    Forms\Components\TextInput::make('title')->required()
                    ->label('Titulo')
                    ->required(),
                ]),
            Fieldset::make('Label')->label('Configurações')
                ->schema([
                    Toggle::make('status')->label('Status da Atividade')->inline(false)->default(true)->required(),
                    TextInput::make('order_number')->label('N° (Ordem)')->numeric()->required()
                ]),
            Fieldset::make('Monitoramento')
                ->schema([
                    Toggle::make('is_monitored')
                        ->label('Monitorado')
                        ->default(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable()->searchable(),
                TextColumn::make('title')->label('Titulo')->sortable()->searchable(),
                TextColumn::make('order_number')->label('N° Ordem')->sortable()->searchable(),
                Tables\Columns\BadgeColumn::make('status')->label('Situação')->badge()->sortable()->searchable(),
            ])
            ->filters([
                    SelectFilter::make('status')
                    ->label('Situação')
                    ->options([
                        '1' => 'Ativos',
                        '0' => 'Inativos',
                    ])->default(1),
                    SelectFilter::make('frequency')
                        ->label('Frequência')
                        ->options([
                            'daily' => 'Diária',
                            'specific_day' => 'Dia específico',
                            '5th_working_day' => '5º Dia Útil',
                            'weekly' => 'Semanalmente',
                            'monthly' => 'Mensal',
                    ]),
                    SelectFilter::make('department')
                        ->label('Departamento')
                        ->options(Department::pluck('name', 'id')->toArray())
                        ->query(function ($query, array $data) {
                            if (isset($data['value'])) {
                                $query->whereHas('departments', function ($query) use ($data) {
                                    $query->where('departments.id', $data['value']);
                                });
                            }
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                ]),
                ExportBulkAction::make()->exporter(ActivityExporter::class)->label('Exportar Atividades'),
                Tables\Actions\BulkAction::make('alterarColunas')
                ->label('Alterar Colunas')
                ->form([
                    // Checkbox e campo de valor para "Departamento"
                    Forms\Components\Checkbox::make('colunas.departments')
                        ->label('Departamento')
                        ->reactive(),

                    Forms\Components\BelongsToManyMultiSelect::make('valores.departments')
                    ->label('Novo valor para Departamento')
                    ->relationship(
                        name: 'departments',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()),
                    )
                    ->visible(fn (Get $get) => $get('colunas.departments')),

                    // Checkbox e campo de valor para "Frequência"
                    Forms\Components\Checkbox::make('colunas.frequency')
                        ->label('Frequência')
                        ->reactive(),
                        Forms\Components\Select::make('valores.frequency')
                        ->label('Novo valor para Frequência')
                        ->options([
                            'daily' => 'Diária',
                            'specific_day' => 'Dia específico',
                            '5th_working_day' => '5º Dia Útil',
                            'weekly' => 'Semanalmente',
                            'monthly' => 'Mensal',
                        ])
                        ->reactive() // Garante que mudanças na seleção sejam capturadas
                        ->required(fn (Get $get) => $get('colunas.frequency'))
                        ->visible(fn (Get $get) => $get('colunas.frequency')),

                    // Checkbox e campo de valor para "Monitorado"
                    Forms\Components\Checkbox::make('colunas.is_monitored')
                        ->label('Monitorado')
                        ->reactive(),
                    Forms\Components\Toggle::make('valores.is_monitored')
                        ->label('Novo valor para Monitorado')
                        ->visible(fn (Get $get) => $get('colunas.is_monitored')),

                    Forms\Components\Checkbox::make('colunas.status')
                        ->label('Status')
                        ->reactive(),
                    Forms\Components\Toggle::make('valores.status')
                        ->label('Novo valor para Status')
                        ->visible(fn (Get $get) => $get('colunas.status')),
                ])
                ->action(function (array $data, \Illuminate\Database\Eloquent\Collection $records) {
                    foreach ($records as $record) {
                        $updates = [];

                        foreach ($data['colunas'] ?? [] as $column => $selected) {
                            if ($selected && isset($data['valores'][$column])) {
                                $updates[$column] = $data['valores'][$column];
                            }
                        }

                        // Tratamento especial para o relacionamento de departamentos
                        if (isset($updates['departments'])) {
                            $departmentIds = array_filter($updates['departments']); // Filtra apenas IDs válidos
                            if (!empty($departmentIds)) {
                                $record->load('departments'); // Garante que o relacionamento esteja carregado
                                $record->departments()->sync($departmentIds);
                            }
                            unset($updates['departments']); // Remove para evitar conflito com o update
                        }

                        // Atualiza os campos restantes no registro
                        if (!empty($updates)) {
                            $record->update($updates);
                        }
                    }
                })
                ->requiresConfirmation()
                ->icon('heroicon-m-pencil-square'),
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
            'index' => Pages\ListActivities::route('/'),
        ];
    }
}
