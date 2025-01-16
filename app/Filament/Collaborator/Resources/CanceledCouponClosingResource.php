<?php

namespace App\Filament\Collaborator\Resources;

use App\Filament\Collaborator\Resources\CanceledCouponClosingResource\Pages;
use App\Filament\Collaborator\Resources\CanceledCouponClosingResource\RelationManagers;


use App\Models\CanceledCouponClosing;

use Filament\Forms;
use Filament\Forms\Form;

use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Facades\Filament;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;


class CanceledCouponClosingResource extends Resource
{
    protected static ?string $model = CanceledCouponClosing::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Cancelamento de Cupom (NFCe)';
    protected static ?string $navigationGroup = 'Processos';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([

                Forms\Components\Select::make('filial_id')
                    ->label('Filial')
                    ->options(fn () => \App\Models\Filial::pluck('name', 'id'))
                    ->required()
                    ->reactive() // Torna o campo reativo
                    ->afterStateUpdated(function (callable $set) {
                        // Quando uma filial for selecionada, desbloqueia os campos
                        $set('is_frozen', false);
                    }),

                    Forms\Components\Select::make('operator_pdv_id')
                    ->label('Operador de PDV')
                    ->relationship(
                        name: 'operator',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, $get) =>
                            $query->where('filial_id', $get('filial_id'))->where('is_active', 0) // Filtra pela filial selecionada
                    )
                    ->disabled(fn ($get) => $get('is_frozen')) // Desabilita o campo se is_frozen for true
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome do Operador')
                            ->unique()
                            ->required(),
                        Forms\Components\Select::make('filial_id')
                            ->label('Selecione a Filial')
                            ->relationship('filial', 'name')
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data) {
                        $operator = \App\Models\OperatorPdv::create([
                            'name' => $data['name'],
                            'enterprise_id' => Filament::getTenant()->id,
                            'filial_id' => $data['filial_id'],
                        ]);
                        return $operator->id;
                    }),

                    Forms\Components\DatePicker::make('closing_date')
                    ->label('Data do Fechamento')
                    ->default(now())
                    ->disabled(fn ($get) => $get('is_frozen'))
                    ->required(),

                    Forms\Components\TextInput::make('valor')
                    ->label('Valor do Cancelamento')
                    ->prefix('R$')
                    ->numeric()
                    ->required()
                    ->rules(['numeric', 'min:0.01'])
                    ->step(0.01)
                    ->disabled(fn ($get) => $get('is_frozen')),

                Forms\Components\TextInput::make('user_name')
                    ->label('Frente de Caixa')
                    ->default(fn () => auth()->user()?->name) // Exibe o nome do usuário logado
                    ->disabled() // Somente leitura
                    ->columnSpan('full'),

                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => auth()->id()) // Define o ID do usuário logado
                    ->required(),

                FileUpload::make('attachments')
                    ->directory('cupons')
                    ->label('Fotos do Cupom')
                    ->columnSpan('full')
                    ->multiple()
                    ->panelLayout('grid')
                    ->required()
                    ->openable()
                    ->visibility('public')  // Garanta que os arquivos sejam acessíveis
                    ->disabled(fn ($get) => $get('is_frozen')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->numeric()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('operator.name')
                    ->label('Operador')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('closing_date')
                    ->label('Data do Fechamento')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor')
                    ->label('Valor')
                    ->prefix('R$')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('closing_date')
                    ->label('Data do Fechamento')
                    ->form([
                        DatePicker::make('closing_date_from')
                            ->label('De'),
                        DatePicker::make('closing_date_to')
                            ->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['closing_date_from'] ?? false) {
                            $query->whereDate('closing_date', '>=', $data['closing_date_from']);
                        }
                        if ($data['closing_date_to'] ?? false) {
                            $query->whereDate('closing_date', '<=', $data['closing_date_to']);
                        }
                    }),
                    Tables\Filters\SelectFilter::make('filial_id')
                    ->label('Selecione a Filial')
                    ->relationship('filial', 'name') // Busca a relação Filial e exibe o nome
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()->modalHeading('Detalhes do Cancelamento de Cupom'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCanceledCouponClosings::route('/'),
        ];
    }
}
