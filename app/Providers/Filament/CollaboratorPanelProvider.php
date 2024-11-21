<?php

namespace App\Providers\Filament;

use Filament\Facades\Filament;
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

use Filament\Navigation\MenuItem;

use App\Models\Enterprise;
use App\Models\User;

use App\Filament\Pages\Tenancy\RegisterEnterprise;
use App\Filament\Pages\Tenancy\EditEnterpriseProfile;

use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

use App\Http\Middleware\CheckNoEnterprise;


class CollaboratorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $user = auth()->user();
        $enterprise = Filament::getTenant();

        return $panel
            ->id('collaborator')
            ->path('collaborator')
            ->registration()
            ->login()
            ->passwordReset()
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/icon/icon.png'))
            ->colors([
                'primary' => Color::Amber,
            ])

            ->font('Inter')
            ->discoverResources(in: app_path('Filament/Collaborator/Resources'), for: 'App\\Filament\\Collaborator\\Resources')
            ->discoverPages(in: app_path('Filament/Collaborator/Pages'), for: 'App\\Filament\\Collaborator\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Collaborator/Widgets'), for: 'App\\Filament\\Collaborator\\Widgets')
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
            ])
            ->viteTheme('resources/css/filament/collaborator/theme.css')
            ->databaseNotifications()
            ->tenant(Enterprise::class, ownershipRelationship: 'enterprise', slugAttribute: 'slug')
            ->tenantRegistration(RegisterEnterprise::class)
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => auth()->user()->name)
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle')
                    //If you are using tenancy need to check with the visible method where ->company() is the relation between the user and tenancy model as you called
                    ->visible(function (): bool {
                        return auth()->user()->enterprises()->exists();
                    }),
            ])
            ->plugins([
                FilamentEditProfilePlugin::make()
                ->setTitle('Meu Perfil')
                ->setNavigationLabel('Meu Perfil')
                ->setNavigationGroup('Grupo de Perfil')
                ->setIcon('heroicon-o-user')
                ->shouldRegisterNavigation(false)
                ->shouldShowDeleteAccountForm(false)
                ->shouldShowSanctumTokens()
                ->shouldShowBrowserSessionsForm()
                ->shouldShowAvatarForm()
            ]);
    }

}
