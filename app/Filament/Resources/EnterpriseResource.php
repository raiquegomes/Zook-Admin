<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Table;

use App\Filament\Resources\EnterpriseResource\Pages;
use App\Filament\Resources\EnterpriseResource\RelationManagers;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Models\Enterprise;

class EnterpriseResource extends Resource
{
    protected static ?string $model = Enterprise::class;

    protected static ?string $navigationLabel = 'Empresa';

    protected static ?string $navigationGroup = 'Gerenciamento';

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $recordTitleAttribute = 'Empresa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cnpj')
                    ->label('CNPJ')
                    ->maxLength(255)
                    ->unique(),
                Forms\Components\TextInput::make('name')
                    ->label('Razão Social')
                    ->reactive()
                    ->debounce(5000)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->maxLength(255),
                Forms\Components\TextInput::make('fantasy_name')
                    ->label('Nome Fantasia')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->unique()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->label('Status')
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->label('URL')
                    ->unique()
                    ->required()
                    ->readonly()
                    ->maxLength(255),
                Forms\Components\TextInput::make('token_invitation')
                    ->label('TOKEN')
                    ->required()
                    ->readonly()
                    ->default(fn() => Str::random(20))
                    ->maxLength(255)
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cnpj')
                ->label('CNPJ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('social_reason')
                ->label('Razão Social')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fantasy_name')
                ->label('Nome Fantasia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                ->label('Email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                ->label('Status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                ->label('Criação')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnterprises::route('/'),
            'create' => Pages\CreateEnterprise::route('/create'),
            'edit' => Pages\EditEnterprise::route('/{record}/edit'),
        ];
    }
}
