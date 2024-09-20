<?php

namespace Xbigdaddyx\Beverly\Filament\Pages;


class CartonDashboard extends \Filament\Pages\Dashboard
{
    protected static string $routePath = 'carton';
    protected static ?string $title = 'Carton Boxes Dashboard';
    protected static ?string $navigationIcon = 'tabler-box';
    public function getColumns(): int|string|array
    {
        return 12;
    }



    public function getWidgets(): array
    {
        return [
            \Xbigdaddyx\Beverly\Filament\Widgets\CartonBoxSummaryChart::class,
            \Xbigdaddyx\Beverly\Filament\Widgets\CartonBoxPercentageTypeChart::class,
            \Xbigdaddyx\Beverly\Filament\Widgets\CartonBoxPoChart::class,
        ];
    }
}
