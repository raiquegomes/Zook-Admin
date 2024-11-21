<?php

namespace App\Filament\Pages\Tenancy;
 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
 
class EditEnterpriseProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Perfil da Empresa';
    }
 
    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Grid::make(2) // Define que haverá 2 colunas
                ->schema([
                    TextInput::make('cnpj')
                        ->label('CNPJ')
                        ->extraInputAttributes(['id' => 'cnpj-input'])
                        ->required(),
                    TextInput::make('name')
                        ->label('Nome da Empresa')
                        ->required(),
                    TextInput::make('fantasy_name')
                        ->label('Nome Fantasia')
                        ->required(),
                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->required(),
                ]),
            ]);
    }
}