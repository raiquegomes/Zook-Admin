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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()->exporter(ActivityExporter::class)->label('Exportar Atividades'),
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
