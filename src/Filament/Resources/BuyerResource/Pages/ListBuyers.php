<?php

namespace Xbigdaddyx\Beverly\Filament\Resources\BuyerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Xbigdaddyx\Beverly\Filament\Resources\BuyerResource;

class ListBuyers extends ListRecords
{
    protected static string $resource = BuyerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
