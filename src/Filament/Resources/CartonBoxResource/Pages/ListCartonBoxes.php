<?php

namespace Xbigdaddyx\Beverly\Filament\Resources\CartonBoxResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Xbigdaddyx\Beverly\Filament\Resources\CartonBoxResource;
use Xbigdaddyx\Beverly\Filament\Widgets\CartonBoxSummaryChart;

class ListCartonBoxes extends ListRecords
{
    protected static string $resource = CartonBoxResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            //CartonBoxSummaryChart::class
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
