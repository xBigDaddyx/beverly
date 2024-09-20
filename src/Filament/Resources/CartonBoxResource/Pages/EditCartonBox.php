<?php

namespace Xbigdaddyx\Beverly\Filament\Resources\CartonBoxResource\Pages;


use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Xbigdaddyx\Beverly\Filament\Resources\CartonBoxResource;

class EditCartonBox extends EditRecord
{
    protected static string $resource = CartonBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
