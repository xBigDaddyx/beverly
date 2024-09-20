<?php

namespace Xbigdaddyx\Beverly\Filament\Resources\BuyerResource\RelationsManager;


use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Xbigdaddyx\Beverly\Filament\Resources\PackingListResource;

class PackingListsRelationManager extends RelationManager
{
    protected static string $relationship = 'packingLists';

    protected static ?string $title = 'Packing List';

    protected static ?string $recordTitleAttribute = 'po';

    public function form(Form $form): Form
    {
        return PackingListResource::form($form);
        // return $form
        //     ->schema([
        //         Forms\Components\Section::make('General Information')
        //             ->schema([
        //                 Forms\Components\TextInput::make('po')
        //                     ->label('PO'),
        //                 Forms\Components\TextInput::make('style_no')
        //                     ->label('Style'),
        //                 Forms\Components\TextInput::make('contract_no')
        //                     ->label('Contract'),
        //                 Forms\Components\TextInput::make('batch')
        //                     ->label('Batch'),
        //                 Forms\Components\Textarea::make('description')
        //                     ->label('Description')
        //                     ->columnSpan(2),

        //             ])->columns(2),
        //     ]);
    }

    public function table(Table $table): Table
    {
        return PackingListResource::table($table)

            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AssociateAction::make()
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant())->withoutGlobalScopes([
                        SoftDeletingScope::class,
                    ])->where('buyer_id', null))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DissociateAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DissociateBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
