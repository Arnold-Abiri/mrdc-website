<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use App\Filament\Pages\Auth\PasswordReset\ResetPassword;
use App\Filament\Pages\CouncilDashboard;
use App\Filament\Pages\Profile;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->passwordReset(
                RequestPasswordReset::class,
                ResetPassword::class,
            )
            ->profile(Profile::class)
            ->multiFactorAuthentication(AppAuthentication::make()->recoverable())
            ->brandName('Mutoko Rural District Council')
            ->brandLogo(fn (): HtmlString => new HtmlString(view('filament.components.admin-brand')->render()))
            ->brandLogoHeight('auto')
            ->colors([
                'primary' => Color::hex('#0B8F62'),
            ])
            ->viteTheme('resources/css/admin.css')
            ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER, fn () => view('filament.components.admin-topbar-actions'))
            ->renderHook(PanelsRenderHook::USER_MENU_AFTER, fn () => view('filament.components.admin-topbar-user'))
            ->navigationGroups([
                NavigationGroup::make('Content')->collapsible(),
                NavigationGroup::make('Council')->collapsible(),
                NavigationGroup::make('Services & Development')->collapsible(),
                NavigationGroup::make('Public Enquiries')->collapsible(),
                NavigationGroup::make('Reports & Monitoring')->collapsible(),
                NavigationGroup::make('Administration')->collapsible(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                CouncilDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
