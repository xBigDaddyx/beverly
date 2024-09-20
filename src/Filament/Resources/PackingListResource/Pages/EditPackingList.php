<?php

namespace Xbigdaddyx\Beverly\Filament\Resources\PackingListResource\Pages;


use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Xbigdaddyx\Beverly\Filament\Resources\PackingListResource;

class EditPackingList extends EditRecord
{
    protected static string $resource = PackingListResource::class;
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

}
