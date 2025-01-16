<?php

namespace App\Filament\Enterprise\Resources;

use App\Filament\Enterprise\Resources\DepartmentResource\Pages;
use App\Filament\Enterprise\Resources\DepartmentResource\RelationManagers;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Departamento';
    protected static ?string $navigationGroup = 'Gerenciamento';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Nome')->required(),
                Forms\Components\TextInput::make('office_day')->label('Dias de Trabalho'),
                Forms\Components\CheckboxList::make('work_days')
                    ->label('Dias Trabalhados')
                    ->options([
                        'Monday' => 'Segunda-Feira',
                        'Tuesday' => 'Terça-Feira',
                        'Wednesday' => 'Quarta-Feira',
                        'Thursday' => 'Quinta-Feira',
                        'Friday' => 'Sexta-Feira',
                        'Saturday' => 'Sabado',
                        'Sunday' => 'Domingo',
                    ])->required(),
                // Campo para inserir as datas de folga
                Forms\Components\TextInput::make('holidays')
                ->label('Datas de Folga (YYYY-MM-DD)')
                ->helperText('Separe as datas com vírgulas (ex: 2024-01-01, 2024-01-08)')
                ->required(fn ($get) => $get('is_scale')), // Tornar este campo obrigatório se o departamento for em escala
                // Adicionando o campo BelongsToMany para vincular usuários
                Forms\Components\BelongsToManyMultiSelect::make('members')
                ->label('Membros')
                ->relationship('members', 'name', function ($query) {
                    // Obtém a empresa atual do usuário logado
                    $currentEnterprise = Filament::getTenant();

                    // Filtra apenas os usuários que pertencem à empresa atual
                    return $query->whereHas('enterprises', function ($query) use ($currentEnterprise) {
                        $query->where('enterprise_id', $currentEnterprise->id);
                    });
                }),
                Forms\Components\Select::make('department_master_id')
                ->label('Departamento Chefe')
                ->relationship(
                    name: 'parentDepartment',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query) => $query->where('enterprise_id', Filament::getTenant()->id),
                ),
                Fieldset::make('Funções')
                ->schema([
                    Forms\Components\Toggle::make('is_scale')
                    ->label('Departamento em Escala')
                    ->required(),
                ])
                ->columns(3),
                Fieldset::make('Widgets')
                ->schema([
                    Forms\Components\Toggle::make('show_notice_board')
                    ->label('Exibir Mural de Avisos')
                    ->default(false),
                    Forms\Components\Toggle::make('show_supplier_count')
                    ->label('Exibir Contagem de Fornecedores Diária')
                    ->default(false),
                ])
                ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nome')->sortable()->searchable(),
                ImageColumn::make('members.avatar_url')->label('Usuarios')
                ->circular()
                ->stacked()
                ->limit(3)
                ->limitedRemainingText(),
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
            'index' => Pages\ListDepartments::route('/'),
        ];
    }
}
