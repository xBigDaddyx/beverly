<?php

namespace Xbigdaddyx\Beverly;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Xbigdaddyx\Beverly\Filament\Resources\BuyerResource;
use Xbigdaddyx\Beverly\Filament\Resources\CartonBoxResource;
use Xbigdaddyx\Beverly\Filament\Resources\PackingListResource;

class BeverlyPlugin implements Plugin
{
    public function getId(): string
    {
        return 'beverly';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            // EditCompanyProfile::class,
            // RegisterCompany::class,

        ])
            ->resources([
               CartonBoxResource::class,
               PackingListResource::class,
               BuyerResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
