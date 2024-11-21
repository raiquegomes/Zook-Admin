<?php

namespace App\Filament\Enterprise\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;

use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                        ->label('Inicio'),
                        DatePicker::make('endDate')
                        ->label('Fim'),
                        // ...
                    ])
                    ->columns(3),
            ]);
    }
}
