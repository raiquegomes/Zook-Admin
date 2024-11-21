<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ActivityStatus: string implements HasColor, HasIcon, HasLabel
{
    case Ativo = '1';

    case Inativo = '0';

    public function getLabel(): string
    {
        return match ($this) {
            self::Ativo => 'Ativo',
            self::Inativo => 'Inativo',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Ativo => 'success',
            self::Inativo => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Ativo => 'heroicon-m-clipboard-document-check',
            self::Inativo => 'heroicon-m-exclamation-triangle',
        };
    }
}
