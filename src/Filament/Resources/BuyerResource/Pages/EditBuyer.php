<?php

namespace Xbigdaddyx\Beverly\Filament\Resources\BuyerResource\Pages;


use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Xbigdaddyx\Beverly\Filament\Resources\BuyerResource;

class EditBuyer extends EditRecord
{
    protected static string $resource = BuyerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
