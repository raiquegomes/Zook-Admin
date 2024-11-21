<?php

namespace App\Filament\Collaborator\Resources;

use App\Filament\Collaborator\Resources\AppointmentSchedulingResource\Pages;
use App\Filament\Collaborator\Resources\AppointmentSchedulingResource\RelationManagers;

use App\Models\AppointmentScheduling;

use Filament\Forms;
use Filament\Forms\Form;

use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

use Filament\Facades\Filament;

class AppointmentSchedulingResource extends Resource
{
    protected static ?string $model = AppointmentScheduling::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Agendamento';
    protected static ?string $navigationGroup = 'Fornecedores';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('supplier_id')
                ->label('Fornecedor')
                ->relationship('supplier', 'name')
                ->required()
                ->searchable(),
            Forms\Components\BelongsToManyMultiSelect::make('departments')
                ->relationship(
                    name: 'departments',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()),
                )
                ->label('Departamento'),
            DatePicker::make('scheduled_date')
                ->label('Data do Agendamento')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier.name')->label('Fornecedor')->searchable(),
                TextColumn::make('scheduled_date')->label('Agendamento')->sortable()->searchable(),
                // Coluna que exibe o status de conclusão de cada usuário
                TextColumn::make('completed_users')
                ->badge()
                ->color('success')
                ->label('Concluiu')
                ->getStateUsing(function (AppointmentScheduling $record) {
                    // Buscar os usuários que marcaram o 'completed_at' na tabela pivô e o número do balanço
                    $completedUsers = $record->users()
                        ->wherePivotNotNull('completed_at')
                        ->get(['name', 'appointment_scheduling_user.balance_id']);

                    // Formatar cada entrada com o nome e número do balanço
                    return $completedUsers->map(function ($user) {
                        return "{$user->name} (N°: {$user->pivot->balance_id})";
                    })->implode(', '); // Exibe o nome e o balanço separados por vírgula
                }),
            ])
            ->defaultSort('scheduled_date', 'desc')
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
            'index' => Pages\ListAppointmentSchedulings::route('/'),
            'create' => Pages\CreateAppointmentScheduling::route('/create'),
        ];
    }
}
