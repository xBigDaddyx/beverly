<?php

namespace Xbigdaddyx\Beverly\Filament\Resources\PackingListResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Xbigdaddyx\Beverly\Filament\Resources\PackingListResource;

class ListPackingLists extends ListRecords
{
    protected static string $resource = PackingListResource::class;
    protected function getActions(): array
    {
        return [
            CreateAction::make()
            ->visible(fn ():bool=>auth()->user()->can('create_packing_list')),
        ];
    }
}
