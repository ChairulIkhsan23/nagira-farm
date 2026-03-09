<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Pengaturan;
use App\Filament\Widgets\AccountInfo;
use App\Filament\Widgets\ArtikelOverview;
use App\Filament\Widgets\TrenPerkawinanChart;
use App\Filament\Widgets\GeneralOverview;
use App\Filament\Widgets\PopulasiKambingChart;
use App\Filament\Widgets\TernakTerbaru;
use App\Filament\Widgets\StatusFatteningChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Navigation\MenuItem;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('48px')
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::Green,
            ])
            ->font('Manrope', provider: GoogleFontProvider::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                Pengaturan::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountInfo::class,
                GeneralOverview::class,
                TernakTerbaru::class,
                PopulasiKambingChart::class,
                StatusFatteningChart::class,
                TrenPerkawinanChart::class,
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
            ->userMenuItems([
            'profile' => MenuItem::make()
                ->label('Pengaturan Akun')
                ->url(fn () => \App\Filament\Pages\Pengaturan::getUrl())
                ->icon('heroicon-o-cog-6-tooth'),
        ]);
    }
}
