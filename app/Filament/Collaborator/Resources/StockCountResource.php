<?php

namespace App\Filament\Collaborator\Resources;

use App\Filament\Collaborator\Resources\StockCountResource\Pages;
use App\Filament\Collaborator\Resources\StockCountResource\RelationManagers;

use App\Models\StockCount;
use App\Models\ListProductsCount;

use Illuminate\Support\Facades\Auth;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockCountResource extends Resource
{
    protected static ?string $model = StockCount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Contagem (Açougue/Hortifruti)';
    protected static ?string $navigationGroup = 'Processos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                        ->label('Usuario')
                        ->default(Auth::user()->id)
                        ->readonly()
                        ->required(),

                Forms\Components\Datepicker::make('date')
                        ->label('Data da Contagem')
                        ->default(now())
                        ->required(),

                Forms\Components\Select::make('filial_id')
                        ->options([
                            '1' => 'Filial 1',
                            '2' => 'Filial 2',
                            '3' => 'Filial 3',
                        ])
                        ->label('Filial')
                        ->required(),

                Forms\Components\Select::make('count_type')
                        ->label('Tipo de Contagem')
                        ->options([
                            'acougue' => 'Açougue',
                            'hortifruti' => 'Hortifruti',
                        ])
                        ->required()
                        ->reactive() // Atualiza o formulário quando alterado
                        ->afterStateUpdated(fn ($state, callable $set) => $set('products', self::getDefaultProducts($state))),


                        Forms\Components\Repeater::make('products')
                        ->relationship('products')
                        ->schema(fn ($get) => [
                            Forms\Components\TextInput::make('name')
                                ->label('Nome do Produto')
                                ->required()
                                ->readonly(),

                            $get('count_type') === 'hortifruti'
                            ? Forms\Components\TextInput::make('boning_stock')
                                ->numeric()
                                ->default(0.00)
                                ->label('Estoque KG')
                                ->required()
                            : Forms\Components\TextInput::make('boning_stock')
                                ->numeric()
                                ->default(0.00)
                                ->label('Estoque Desossa')
                                ->required(),

                            $get('count_type') === 'acougue'
                                ? Forms\Components\TextInput::make('cashier_stock')
                                    ->numeric()
                                    ->default(0.00)
                                    ->label('Estoque Caixaria')
                                    ->required()
                                : Forms\Components\TextInput::make('cashier_stock')
                                    ->numeric()
                                    ->default(0.00)
                                    ->label('Estoque Caixaria')
                                    ->hidden(),

                            Forms\Components\Select::make('quality')
                                ->label('Qualidade')
                                ->options([
                                    'bom' => 'Bom',
                                    'muito_bom' => 'Muito bom',
                                    'ruim' => 'Ruim',
                                ]),
                        ])
                        ->columns(4)
                        ->columnSpanFull()
                        ->label('Produtos'),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Data de Referência')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('filial.name')
                    ->label('Filial')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data da Criação')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ultima Atualização')
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

    protected static function getDefaultProducts(?string $countType): array
    {
        if (!$countType) {
            return [];
        }

        return ListProductsCount::query()
            ->where('type', $countType) // Filtra os produtos pelo tipo de contagem
            ->get()
            ->map(fn ($product) => [
                'name' => $product->name,
                'boning_stock' => '0.00',
                'cashier_stock' => $countType === 'acougue' ? '0.00' : null, // Define valores padrão
                'quality' => $countType === 'hortifruti' ? null : null, // Valor padrão para qualidade
            ])
            ->toArray();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockCounts::route('/'),
            'create' => Pages\CreateStockCount::route('/create'),
            'edit' => Pages\EditStockCount::route('/{record}/edit'),
        ];
    }
}
