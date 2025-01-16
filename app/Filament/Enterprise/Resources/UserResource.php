<?php

namespace App\Filament\Enterprise\Resources;

use Filament\Facades\Filament;

use App\Filament\Enterprise\Resources\UserResource\Pages;
use App\Filament\Enterprise\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Colaborador';
    protected static ?string $navigationGroup = 'Gestão';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    // Relacionamento com os tenants configurado
    protected static ?string $tenantOwnershipRelationshipName = 'enterprises';


        public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
        {
            // Filtra os usuários pelo tenant ativo automaticamente
            return parent::getEloquentQuery();
        }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required()
                ->label('Nome'),

                Forms\Components\TextInput::make('email')
                ->required()
                ->label('Email'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nome'),
                TextColumn::make('email')->label('Email'),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('removeEnterprise')
                ->label('Remover da Empresa')
                ->action(function (User $record, array $data) {
                    $record->removeFromEnterprise($data['enterprise_id']);
                })
                ->form([
                    Forms\Components\Select::make('enterprise_id')
                        ->label('Empresa')
                        ->options(fn (User $record) => $record->enterprises->pluck('name', 'id'))
                        ->required(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
