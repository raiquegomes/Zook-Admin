<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UserActivityUser: string implements HasColor, HasIcon, HasLabel
{
    case Concluido = 'concluido';

    case Analise = 'em_analise';

    case Rejeitado = 'nao_aprovado';

    case Aprovado = 'aprovado';

    case Expirado = 'nao_concluido';

    public function getLabel(): string
    {
        return match ($this) {
            self::Concluido => 'Concluido',
            self::Analise => 'Analise',
            self::Rejeitado => 'Rejeitado',
            self::Aprovado => 'Aprovado',
            self::Expirado => 'Não concluido',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Concluido => 'success',
            self::Analise => 'info',
            self::Rejeitado => 'warning',
            self::Aprovado => 'success',
            self::Expirado => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Aprovado => 'heroicon-m-clipboard-document-check',
            self::Expirado => 'heroicon-m-x-mark',
            self::Rejeitado => 'heroicon-m-exclamation-triangle',
            self::Analise => 'heroicon-m-magnifying-glass-circle',
            self::Concluido => 'heroicon-m-check',
        };
    }
}