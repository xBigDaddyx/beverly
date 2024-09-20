<?php

namespace Xbigdaddyx\Beverly\Filament\Resources\PackingListResource\Pages;


use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Xbigdaddyx\Beverly\Filament\Resources\PackingListResource;

class ViewPackingList extends ViewRecord
{
    protected static string $resource = PackingListResource::class;
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
