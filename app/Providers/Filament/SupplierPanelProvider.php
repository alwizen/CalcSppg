<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Resma\FilamentAwinTheme\FilamentAwinTheme;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;

class SupplierPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('supplier')
            ->path('supplier')
            ->brandName('Portal Supplier')
            ->favicon(asset('img/fav.png'))
            ->authGuard('suppliers')
            ->login()
            ->font('Poppins')
            ->topNavigation()
            ->colors([
                'primary' => '#8839ef',
            ])
            ->discoverResources(in: app_path('Filament/Supplier/Resources'), for: 'App\Filament\Supplier\Resources')
            ->discoverPages(in: app_path('Filament/Supplier/Pages'), for: 'App\Filament\Supplier\Pages')
            // ->pages([
            //     Dashboard::class,
            // ])
            ->discoverWidgets(in: app_path('Filament/Supplier/Widgets'), for: 'App\Filament\Supplier\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->plugins([
                FilamentAwinTheme::make()
                    ->primaryColor('#8839ef'),
                FilamentBackgroundsPlugin::make()
                    // ->showAttribution(false)
                    ->imageProvider(
                        MyImages::make()
                            ->directory('images/bg/sup')
                    ),
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
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
