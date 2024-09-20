<?php

namespace Xbigdaddyx\Beverly\Filament\Resources;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Infolist;
use Filament\Resources\Components\Tab;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Xbigdaddyx\Beverly\Models\Buyer;

class BuyerResource extends Resource
{
    protected static ?string $model = Buyer::class;
    protected static ?string $navigationIcon = 'tabler-basket';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $label = 'Buyers';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('General Information')
            ->description('Information about this buyer')
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                        ->label('logo')
                        ->directory('logos')
                        ->columnSpanFull()
                        ->getUploadedFileNameForStorageUsing(
                            fn(TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                ->prepend(Auth::user()->name . '-'),
                        )
                        ->downloadable()
                        ->image()
                        ->imageEditor()
                        ->grow(false),
                        Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label(__('Buyer Name')),
                        Forms\Components\TextInput::make('country')
                            ->label(__('Buyer Country')),
                    ])

            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->recordTitleAttribute('panel_path')
            ->description('Buyers information for packing lists.')
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\ImageColumn::make('logo')
                    ->grow(false),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('name')
                        ->searchable()
                        ->label(__('Buyer Name'))->weight(FontWeight::Bold),
                        Tables\Columns\TextColumn::make('company.name')
                        ->formatStateUsing(fn ($state):string=> 'Owner : '.$state)
                        ->searchable()
                        ->label(__('Owner')),

                    ]),

                ]),

                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\TextColumn::make('country')
                    ->searchable()
                    ->label(__('Buyer Country')),
                    Tables\Columns\TextColumn::make('creator.name')
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn ($state):string=> 'Creator : '.$state)
                    ->searchable()
                    ->label(__('Created By')),
                    Tables\Columns\TextColumn::make('created_at')
                    ->searchable()
                    ->label(__('Created At')),
                ])->collapsible(),
            ])


        ->filters([
            Tables\Filters\TrashedFilter::make(),
        ])
        ->actions([
                Tables\Actions\EditAction::make()
                ->visible(fn (): bool => auth()->user()->can('buyer_update')),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn (): bool => auth()->user()->can('buyer_delete')),

        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn (): bool => auth()->user()->can('buyer_deleteBulk')),
                Tables\Actions\ForceDeleteBulkAction::make()
                    ->visible(fn (): bool => auth()->user()->can('buyer_deleteBulk')),
                Tables\Actions\RestoreBulkAction::make()
                    ->visible(fn (): bool => auth()->user()->can('buyer_restoreBulk')),
            ]),
        ]);
    }
    public static function getRelations(): array
    {
        return [
         BuyerResource\RelationsManager\PackingListsRelationManager::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => BuyerResource\Pages\ListBuyers::route('/'),
            'create' => BuyerResource\Pages\CreateBuyer::route('/create'),
            'edit' => BuyerResource\Pages\EditBuyer::route('/{record}/edit'),
        ];
    }
}
