<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use App\Models\Enterprise;

use App\Filament\Pages\Tenancy\RegisterEnterprise;
use App\Filament\Pages\Tenancy\EditEnterpriseProfile;

use Filament\Navigation\MenuItem;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use App\Http\Middleware\CheckAdminRole;


class EnterprisePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('enterprise')
            ->path('enterprise')
            ->login()
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/icon/icon.png'))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Enterprise/Resources'), for: 'App\\Filament\\Enterprise\\Resources')
            ->discoverPages(in: app_path('Filament/Enterprise/Pages'), for: 'App\\Filament\\Enterprise\\Pages')
            ->pages([

            ])
            ->discoverWidgets(in: app_path('Filament/Enterprise/Widgets'), for: 'App\\Filament\\Enterprise\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                CheckAdminRole::class,
            ])
            ->tenant(Enterprise::class, ownershipRelationship: 'enterprise', slugAttribute: 'slug')
            ->tenantRegistration(RegisterEnterprise::class)
            ->tenantProfile(EditEnterpriseProfile::class)
            ->databaseNotifications();
    }
    public function resources(): array
    {
        return [
            SomeResource::class,
            AnotherResource::class,
            // Adicione outros recursos aqui se necessário
        ];
    }
}
