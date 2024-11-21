<?php

namespace App\Filament\Enterprise\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Models\Enterprise;
use App\Models\EnterpriseUser;

class InvitationCodeWidget extends Widget
{
    protected static string $view = 'filament.enterprise.widgets.invitation-code-widget';

    public $code;

    // Método para processar o código de convite
    public function submit()
    {
        $enterprise = Enterprise::where('token_invitation', $this->code)->first();

        if (!$enterprise) {
            Notification::make()
                ->title('Convite Inválido')
                ->danger()
                ->body('O código de convite fornecido é inválido.')
                ->send();
            return;
        }

        $user = auth()->user();

        if ($user->enterprises->contains($enterprise)) {
            Notification::make()
                ->title('Já está na Coorporação')
                ->warning()
                ->body('Você já faz parte da empresa associada a este convite.')
                ->send();
            return;
        }
        
        $enterprise->users()->attach($user->id);


        Notification::make()
            ->title('Convite Aceito')
            ->success()
            ->body('Você agora faz parte da empresa!')
            ->send();
    }
}
