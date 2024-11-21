<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Component;

class RegisterTenancy extends Component
{
    protected string $view = 'forms.components.register-tenancy';

    public static function make(): static
    {
        return app(static::class);
    }
}
