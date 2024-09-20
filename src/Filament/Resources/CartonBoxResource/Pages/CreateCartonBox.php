<?php

namespace Xbigdaddyx\Beverly\Filament\Resources\CartonBoxResource\Pages;


use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Filament\Forms\Get;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Xbigdaddyx\Beverly\Filament\Resources\CartonBoxResource;

class CreateCartonBox extends CreateRecord
{
    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
    protected static string $resource = CartonBoxResource::class;
}
