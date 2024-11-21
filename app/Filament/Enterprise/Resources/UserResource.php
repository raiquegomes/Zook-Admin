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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function getEloquentQuery(): Builder
    {
        $enterprise = Filament::getTenant();

        return parent::getEloquentQuery()->whereHas('enterprise', function ($query) {
            $query->where('id', $enterprise->id); // Supondo que o multi-tenancy utiliza o `currentTenant` para a empresa atual
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nome'),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('enterprise.name')->label('Empresa'),
            ])
            ->filters([
                Filter::make('Empresa')
                    ->query(fn (Builder $query) => $query->whereHas('enterprise', fn ($q) => $q->where('id', auth()->user()->currentTenant->id))),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
