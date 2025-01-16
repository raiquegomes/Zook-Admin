<?php

namespace App\Filament\Enterprise\Resources;

use App\Filament\Enterprise\Resources\PerformanceResource\Pages;
use App\Filament\Enterprise\Resources\PerformanceResource\RelationManagers;
use App\Models\Performance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextInputColumn;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PerformanceResource extends Resource
{
    protected static ?string $model = Performance::class;

    protected static ?string $navigationLabel = 'Aval. de Performace';
    protected static ?string $navigationGroup = 'Gerenciamento da Empresa';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('completion_percentage')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('ID do Usuario')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Data')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextInputColumn::make('completion_percentage')
                    ->label('% Atingido')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data da Criação')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ultima Alteração')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable(),

                    Tables\Filters\Filter::make('date')
                    ->label('Data do Fechamento')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('De'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['date_from'] ?? false) {
                            $query->whereDate('date', '>=', $data['date_from']);
                        }
                        if ($data['date_to'] ?? false) {
                            $query->whereDate('date', '<=', $data['date_to']);
                        }
                    })
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
            'index' => Pages\ListPerformances::route('/'),
        ];
    }
}
