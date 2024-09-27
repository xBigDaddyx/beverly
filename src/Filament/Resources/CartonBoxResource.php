<?php

namespace Xbigdaddyx\Beverly\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Awcodes\Shout\Components\Shout;
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
use Filament\Tables\Actions\HeaderActionsPosition;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;
use stdClass;
use Xbigdaddyx\Beverly\Filament\Resources\PackingListResource\RelationsManager\CartonBoxesRelationManager;
use Xbigdaddyx\Beverly\Models\Buyer;
use Xbigdaddyx\Beverly\Models\CartonBox;
use Xbigdaddyx\Beverly\Models\PackingList;

class CartonBoxResource extends Resource
{
    protected static ?string $model = CartonBox::class;
    protected static ?string $navigationIcon = 'tabler-box';

    protected static ?string $navigationGroup = 'Boxes';

    protected static ?string $label = 'Carton Box';
    public static function infolist(Infolist $list): Infolist
    {
        return $list
            ->schema([
                Infolists\Components\Split::make([
                    Infolists\Components\Section::make('Carton Box Information')
                        ->id('carton-box-information')
                        ->icon('sui-box')
                        ->description('General information for this carton box.')
                        ->schema([

                            Infolists\Components\TextEntry::make('box_code')
                                ->inlineLabel()
                                ->color('secondary')
                                ->copyable()
                                ->copyMessage('Copied!')
                                ->copyMessageDuration(1500)
                                ->weight(\Filament\Support\Enums\FontWeight::Bold)
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                ->iconColor('primary')
                                ->icon('tabler-barcode'),
                            Infolists\Components\TextEntry::make('packingList.po')
                                ->inlineLabel()
                                ->color('secondary')
                                ->copyable()
                                ->copyMessage('Copied!')
                                ->copyMessageDuration(1500)
                                ->weight(\Filament\Support\Enums\FontWeight::Bold)
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                ->iconColor('primary')
                                ->icon('tabler-file-invoice')
                                ->label('PO'),
                            Infolists\Components\TextEntry::make('type')
                                ->inlineLabel()
                                ->icon('tabler-badge')
                                ->badge()
                                ->color(fn(string $state): string => match ($state) {
                                    'SOLID' => 'danger',
                                    'MIX' => 'warning',
                                    'RATIO' => 'success',
                                }),
                            Infolists\Components\TextEntry::make('carton_number')
                                ->inlineLabel()
                                ->iconColor('primary')
                                ->icon('tabler-number'),
                            Infolists\Components\TextEntry::make('quantity')
                                ->inlineLabel()
                                ->iconColor('primary')
                                ->icon('tabler-number')
                                ->numeric(),
                            Infolists\Components\IconEntry::make('is_completed')
                                ->tooltip(fn(Model $record): string => $record->completed_at . ' ( ' . $record->completedBy->name . ' )')
                                ->inlineLabel()
                                ->label('Completed')
                                ->boolean()
                                ->trueColor('secondary')
                                ->falseColor('danger'),
                            Infolists\Components\TextEntry::make('description')
                                ->inlineLabel()
                                ->iconColor('primary')
                                ->icon('tabler-pencil')
                                ->formatStateUsing(fn(string $state): HtmlString => new HtmlString($state))
                                ->columnSpanFull(),
                        ])->columns(2),
                    Infolists\Components\Section::make([
                        Infolists\Components\TextEntry::make('created_at')
                            ->iconColor('warning')
                            ->icon('tabler-calendar')
                            ->dateTimeTooltip()
                            ->since(),
                        // ->formatStateUsing(fn (Model $record):string=> $record->created_at.' ( '.$record->creator->name.' )'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->iconColor('warning')
                            ->icon('tabler-calendar')
                            ->dateTimeTooltip()
                            ->since(),
                        // ->formatStateUsing(fn (Model $record):string=> $record->updated_at.' ( '.$record->editor->name.' )'),
                        Infolists\Components\TextEntry::make('completed_at')
                            ->iconColor('warning')
                            ->icon('tabler-calendar')
                            ->dateTimeTooltip()
                            ->since()
                            // ->formatStateUsing(fn(Model $record): string => $record->completed_at . ' ( ' . $record->completedBy->name . ' )')
                            ->hidden(fn($state): bool => $state === null),
                        Infolists\Components\TextEntry::make('deleted_at')
                            ->iconColor('warning')
                            ->icon('tabler-calendar')
                            ->formatStateUsing(fn(Model $record): string => $record->deleted_at . ' ( ' . $record->deletedBy->name . ' )')
                            ->hidden(fn($state): bool => $state === null),
                    ])
                        ->id('timestamps')->grow(false),
                ])->from('md'),
            ]);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Shout::make('important')
                //         ->hiddenOn('create')
                //         ->visible(fn (Model $record): bool => $record->isLocked())
                //         ->columnSpan('full')
                //         ->icon('tabler-lock')
                //         ->content('This carton box is locked because its already completed!')
                //         ->type('warning'),
                Forms\Components\Section::make('General Information')
                    ->schema([
                        Forms\Components\TextInput::make('box_code')
                            ->label('Box Code')
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->live()
                            ->required()
                            ->options([
                                'SOLID' => 'SOLID',
                                'MULTIPLE' => 'MULTIPLE',
                                'MIX' => 'MIX',
                                'RATIO' => 'RATIO',
                            ])
                            ->label('Type'),
                        Forms\Components\Select::make('packing_list_id')
                            ->hiddenOn(CartonBoxesRelationManager::class)
                            ->relationship('packingList', 'po', modifyQueryUsing: fn(Builder $query) => $query->whereBelongsTo(Filament::getTenant())->with('buyer'))
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn(Model $record) => "PO: {$record->po} - {$record->buyer->name} {$record->buyer->country} - {$record->style_no}"),
                        Forms\Components\TextInput::make('carton_number')
                            ->default(0)
                            ->label('Carton Number'),
                        // Forms\Components\TextInput::make('size')
                        //     ->hidden(fn(Get $get): bool => $get('type') === 'RATIO')
                        //     ->label('Size'),
                        // Forms\Components\TextInput::make('color')
                        //     ->hidden(fn(Get $get): bool => $get('type') === 'RATIO')
                        //     ->label('Color'),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->label('Quantity'),

                        Forms\Components\Toggle::make('is_completed')
                            ->label('Completed')
                            ->hiddenOn('create')
                            ->visible(function (Model $record) {
                                if ($record->polybags->count() > 0) {
                                    if ($record->is_completed !== true) {
                                        return true;
                                    }

                                    return false;
                                }

                                return true;
                            }),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActionsPosition(HeaderActionsPosition::Bottom)
            ->columns([

                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->state(
                        static function (Tables\Contracts\HasTable $livewire, stdClass $rowLoop): string {
                            return (string) (
                                $rowLoop->iteration +
                                ($livewire->getTableRecordsPerPage() * (
                                    $livewire->getTablePage() - 1
                                ))
                            );
                        }
                    ),
                Tables\Columns\TextColumn::make('box_code')
                    ->copyable()
                    ->copyMessage('Barcode or Box Code copied!')
                    ->copyMessageDuration(1500)
                    ->color('secondary')
                    ->icon('tabler-barcode')
                    ->iconColor('secondary')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->searchable()
                    ->label('Box Code'),

                Tables\Columns\TextColumn::make('packingList.po')
                    ->copyable()
                    ->copyMessage('PO copied!')
                    ->copyMessageDuration(1500)
                    ->color('secondary')
                    ->description(fn(Model $record): string => 'Style : ' . $record->packingList->style_no . ' / MO : ' . $record->packingList->contract_no)
                    ->icon('tabler-file-invoice')
                    ->iconColor('secondary')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->label('PO'),
                Tables\Columns\TextColumn::make('packingList.buyer.name')
                    ->icon('tabler-basket')
                    ->iconColor('warning')
                    ->label('Buyer'),
                Tables\Columns\TextColumn::make('carton_number')
                    ->iconColor('primary')
                    ->icon('tabler-number')
                    ->tooltip('Carton Number')
                    ->label('CN'),
                Tables\Columns\TextColumn::make('quantity')
                    ->iconColor('primary')
                    ->icon('tabler-number')
                    // ->summarize(Sum::make()->label('Total'))
                    ->label('Quantity'),
                Tables\Columns\TextColumn::make('type')
                    ->icon('tabler-badge')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'SOLID' => 'danger',
                        'MIX' => 'warning',
                        'RATIO' => 'success',
                    })
                    ->searchable()
                    ->label('Type'),
                // Tables\Columns\TextColumn::make('description')
                //     ->searchable()
                //     ->limit(50)
                //     ->label('Box Info'),
                Tables\Columns\IconColumn::make('is_completed')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->boolean()
                    ->trueIcon('tabler-clipboard-check')
                    ->falseIcon('tabler-clipboard-x')
                    ->label('Completed'),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created By'),
                Tables\Columns\TextColumn::make('completed_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Completed At')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('completedBy.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Completed By'),
                // Tables\Columns\TextColumn::make('inspection_requested_by')
                //     ->searchable()
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->label('Inspection Requester'),
                // Tables\Columns\TextColumn::make('inspection_at')
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->label('Inspection At')
                //     ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('po')
                    ->label('Purchase Order')
                    ->searchable()
                    ->relationship('packingLists', 'po'),
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'SOLID' => 'SOLID',
                        'MULTIPLE' => 'MULTIPLE',
                        'MIX' => 'MIX',
                        'RATIO' => 'RATIO',
                    ]),
                Tables\Filters\Filter::make('buyer')
                    ->columnSpanFull()
                    ->columns(3)
                    ->form([
                        Forms\Components\Select::make('buyer_id')
                            ->reactive()
                            ->label('Buyer')
                            ->hint('Buyer Filter')
                            ->hintIcon('tabler-selector')
                            ->hintColor('primary')
                            ->options(fn() => Buyer::whereBelongsTo(Filament::getTenant())->pluck('name', 'id'))
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $buyer = Buyer::find($state);

                                if ($buyer) {
                                    $contract_no = (int) $get('contract_no');

                                    if ($contract_no && $packing_list = packingList::where('contract_no', $contract_no)->first()) {
                                        if ($packing_list->buyer_id !== $buyer->id) {
                                            // aircraft doesn't belong to buyer, so unselect it
                                            $set('contract_no', null);
                                        }
                                    }
                                }
                            }),
                        Forms\Components\Select::make('contract_no')
                            ->hint('Contract / MO Filter')
                            ->hintIcon('tabler-selector')
                            ->hintColor('primary')
                            ->options(function (callable $get, callable $set) {
                                $buyer = Buyer::find($get('buyer_id'));


                                if ($buyer) {
                                    return $buyer->packingLists->pluck('contract_no', 'contract_no');
                                }


                                return PackingList::whereBelongsTo(Filament::getTenant())->pluck('contract_no', 'contract_no');
                            }),
                        Forms\Components\Select::make('style_no')
                            ->hint('Style Filter')
                            ->hintIcon('tabler-selector')
                            ->hintColor('primary')
                            ->options(function (callable $get, callable $set) {
                                $buyer = Buyer::find($get('buyer_id'));


                                if ($buyer && $get('contract_no')) {
                                    return $buyer->packingLists->where('contract_no', $get('contract_no'))->pluck('style_no', 'style_no');
                                }


                                return PackingList::whereBelongsTo(Filament::getTenant())->pluck('style_no', 'style_no');
                            }),


                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['buyer_id'],
                                fn(Builder $query, $buyer): Builder => $query->whereHas('packingList', function (Builder $q) use ($buyer) {
                                    $q->where('buyer_id', '=', $buyer);
                                }),
                            )
                            ->when(
                                $data['contract_no'],
                                fn(Builder $query, $type): Builder => $query->whereHas('packingList', function (Builder $query) use ($data) {
                                    $query->where('contract_no', '=', $data['contract_no']);
                                })
                            )
                            ->when(
                                $data['style_no'],
                                fn(Builder $query, $type): Builder => $query->whereHas('packingList', function (Builder $query) use ($data) {
                                    $query->where('style_no', '=', $data['style_no']);
                                })
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['buyer_id']) {
                            return null;
                        }
                        $buyer = Buyer::find($data['buyer_id']);

                        return 'Buyer : (' . $buyer->name . ')';
                    }),
            ], FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(4)
            ->filtersFormWidth('4xl')
            ->actions([
                \Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction::make('Activities')
                    // ->visible(fn(): bool => auth()->user()->can('view_carton_box_activities'))
                    ->timelineIcons([
                        'created' => 'heroicon-m-check-badge',
                        'updated' => 'heroicon-m-pencil-square',
                    ])
                    ->timelineIconColors([
                        'created' => 'info',
                        'updated' => 'warning',
                    ])
                    ->withRelations(['packingList']),

                // Tables\Actions\Action::make('finish_inspection')
                //     ->visible(fn (CartonBox $record): bool => $record->in_inspection === true)
                //     ->action(fn (CartonBox $record) => $record->finishInspection())
                //     ->icon('heroicon-o-clipboard-document-check')
                //     ->requiresConfirmation()
                //     ->modalHeading('Finish inspection')
                //     ->modalDescription('Are you sure you\'d like to finish inspect this carton box?')
                //     ->modalIcon('heroicon-o-clipboard-document-check')
                //     ->modalIconColor('warning')
                //     ->modalSubmitActionLabel('Yes, finish it.')
                //     ->color('warning'),
                // Tables\Actions\Action::make('inspection')
                //     ->form([
                //         Forms\Components\TextInput::make('inspection_requested_by')
                //             ->label('Inspection Requester')
                //             ->required(),
                //     ])
                //     ->visible(fn (CartonBox $record): bool => $record->is_completed === true)
                //     ->action(fn (array $data, CartonBox $record) => $record->inspection($data['inspection_requested_by']))
                //     ->icon('heroicon-o-document-magnifying-glass')
                //     ->requiresConfirmation()
                //     ->modalHeading('Inspect Carton Box')
                //     ->modalDescription('Are you sure you\'d like to inspect this carton box?')
                //     ->modalIcon('heroicon-o-document-magnifying-glass')
                //     ->modalIconColor('warning')
                //     ->modalSubmitActionLabel('Yes, inspect it.')
                //     ->color('warning'),
                // Tables\Actions\Action::make('lock')
                //     ->action(fn (CartonBox $record) => $record->lock())
                //     ->icon('tabler-lock')
                //     ->requiresConfirmation()
                //     ->visible(fn (CartonBox $record): bool => $record->isUnlocked() && $record->is_completed === true && auth()->user()->can('carton_box_lock', $record))
                //     ->modalHeading('Lock Carton Box')
                //     ->modalDescription('Are you sure you\'d like to lock this carton box?')
                //     ->modalIcon('tabler-lock')
                //     ->modalIconColor('danger')
                //     ->modalSubmitActionLabel('Yes, lock it.')
                //     ->color('danger'),
                // Tables\Actions\Action::make('unlock')
                //     ->action(fn (Model $record) => $record->unlock())
                //     ->icon('tabler-lock-open')
                //     ->visible(fn (Model $record): bool => $record->isLocked() && auth()->user()->can('carton_box_unlock', $record))
                //     ->requiresConfirmation()
                //     ->modalHeading('Unlock Carton Box')
                //     ->modalDescription('Are you sure you\'d like to unlock this carton box?')
                //     ->modalIcon('tabler-lock-open')
                //     ->modalIconColor('success')
                //     ->modalSubmitActionLabel('Yes, Unlock it.')
                //     ->color('success'),
                Tables\Actions\ViewAction::make()
                    ->visible(fn(): bool => auth()->user()->can('viewAny_carton_box'))
                    ->color('secondary'),
                Tables\Actions\EditAction::make()
                    ->visible(fn(CartonBox $record): bool => auth()->user()->can('update_carton_box') && $record->locked_at === null)
                    ->color('warning'),
                // ->visible(fn (CartonBox $record): bool => $record->isUnlocked() && $record->in_inspection === false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn(CartonBox $record): bool => auth()->user()->can('delete_carton_box') && $record->locked_at === null),
                // Tables\Actions\Action::make('view_activities')
                //     ->visible(fn (): bool => auth('ldap')->user()->hasRole('super-admin'))
                //     ->label('Activities')
                //     ->icon('heroicon-m-bolt')
                //     ->color('info')
                //     ->url(fn ($record) => CartonBoxResource::getUrl('activities', ['record' => $record])),


            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->visible(fn(): bool => auth()->user()->can('export_carton_box')),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('Export')
                    ->visible(fn(): bool => auth()->user()->can('export_carton_box')),
                // Tables\Actions\BulkAction::make('finish_inspection')
                //     // ->visible(fn (Collection $records): bool => $records->contains('in_inspection', true))
                //     ->action(fn (Collection $records) => $records->each->finishInspection())
                //     ->icon('heroicon-o-clipboard-document-check')
                //     ->requiresConfirmation()
                //     ->modalHeading('Finish inspection')
                //     ->modalDescription('Are you sure you\'d like to finish inspect this carton box?')
                //     ->modalIcon('heroicon-o-clipboard-document-check')
                //     ->modalIconColor('warning')
                //     ->modalSubmitActionLabel('Yes, finish it.')
                //     ->color('warning'),
                // Tables\Actions\BulkAction::make('inspection')
                //     ->form([
                //         Forms\Components\TextInput::make('inspection_requested_by')
                //             ->label('Inspection Requester')
                //             ->required(),
                //     ])

                //     ->action(fn (array $data, Collection $records) => $records->each->inspection($data['inspection_requested_by']))
                //     ->icon('heroicon-o-document-magnifying-glass')
                //     ->requiresConfirmation()
                //     ->modalHeading('Inspect Carton Box')
                //     ->modalDescription('Are you sure you\'d like to inspect this carton box?')
                //     ->modalIcon('heroicon-o-document-magnifying-glass')
                //     ->modalIconColor('warning')
                //     ->modalSubmitActionLabel('Yes, inspect it.')
                //     ->color('warning'),

                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn(): bool => auth()->user()->can('delete_bulk_carton_box')),
                Tables\Actions\ForceDeleteBulkAction::make()
                    ->visible(fn(): bool => auth()->user()->can('force_delete_bulk_carton_box')),
                Tables\Actions\RestoreBulkAction::make()
                    ->visible(fn(): bool => auth()->user()->can('restore_bulk_carton_box')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationGroup::make('Polybags', [
            \Xbigdaddyx\BeverlySolid\Filament\Pages\SolidPolybagsRelationManager::class,
            // ]),
            CartonBoxResource\RelationsManager\CartonBoxAttributesRelationManager::class,
            // CartonBoxResource\RelationsManager\PolybagsRelationManager::class,
            // CartonBoxResource\RelationsManager\TagsRelationManager::class,

        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => CartonBoxResource\Pages\ListCartonBoxes::route('/'),
            'create' => CartonBoxResource\Pages\CreateCartonBox::route('/create'),
            'view' => CartonBoxResource\Pages\ViewCartonBox::route('/{record}'),
            'edit' => CartonBoxResource\Pages\EditCartonBox::route('/{record}/edit'),
        ];
    }
}
