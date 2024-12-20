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
use Illuminate\Database\Eloquent\Collection;
use Xbigdaddyx\Beverly\Models\Buyer;
use Xbigdaddyx\Beverly\Models\PackingList;

class PackingListResource extends Resource
{
    protected static ?string $model = PackingList::class;
    protected static ?string $navigationIcon = 'tabler-checkup-list';

    protected static ?string $navigationGroup = 'Boxes';

    protected static ?string $label = 'Packing Lists';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Buyer Information')
                    ->schema([
                        Forms\Components\Select::make('buyer_id')
                            ->required()
                            ->relationship('buyer', 'name', modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()))
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} - {$record->country}"),
                        Forms\Components\TextInput::make('po')
                            ->required()
                            ->label('Buyer PO'),
                        Forms\Components\TextInput::make('batch')
                            ->label('Batch'),

                    ])->columns(2),
                Forms\Components\Section::make('General Information')
                    ->schema([
                        Forms\Components\TextInput::make('style_no')
                            ->required()
                            ->label('Style'),
                        Forms\Components\TextInput::make('contract_no')
                            ->required()
                            ->label('Contract'),
                        Forms\Components\Radio::make('type')
                            ->options([
                                'SOLID' => 'Solid',
                                'MULTIPLE' => 'Multiple',
                                'RATIO' => 'Ratio',
                            ])
                            ->inline()
                            ->descriptions([
                                'SOLID' => 'for solid type',
                                'MULTIPLE' => 'there is multiple carton box type in one packing list.',
                                'RATIO' => 'there is ratio attribute for all carton box in one packing list.'
                            ])
                            ->label('Type'),
                        Forms\Components\Textarea::make('description')
                            ->columnSpan(2)
                            ->label('Description'),

                    ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('po')
                    ->searchable()
                    ->sortable()
                    ->label('PO'),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable()
                    ->label('Type'),
                Tables\Columns\TextColumn::make('buyer.name')
                    ->sortable()
                    ->label('Buyer'),
                Tables\Columns\TextColumn::make('buyer.country')
                    ->sortable()
                    ->label('Buyer Country'),
                Tables\Columns\TextColumn::make('style_no')
                    ->searchable()
                    ->sortable()
                    ->label('Style'),
                Tables\Columns\TextColumn::make('contract_no')
                    ->searchable()
                    ->sortable()
                    ->label('Contract'),
                Tables\Columns\TextColumn::make('batch')
                    ->searchable()
                    ->sortable()
                    ->label('Batch'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->label('Description'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created'),
        ])
        ->filters([
            Tables\Filters\TrashedFilter::make()
                ->visible(fn (): bool => auth()->user()->hasRole('super-admin')),
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
                        ->options(fn () => Buyer::pluck('name', 'id'))
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $buyer = Buyer::find($state);

                            if ($buyer) {
                                $contract_no = (int) $get('contract_no');

                                if ($contract_no && $packing_list = PackingList::where('contract_no', $contract_no)->first()) {
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
                                return $buyer->lists->pluck('contract_no', 'contract_no');
                            }


                            return PackingList::all()->pluck('contract_no', 'contract_no');
                        }),
                    Forms\Components\Select::make('style_no')
                        ->hint('Contract / MO Filter')
                        ->hintIcon('tabler-selector')
                        ->hintColor('primary')
                        ->options(function (callable $get, callable $set) {
                            $buyer = Buyer::find($get('buyer_id'));

                            // if a buyer is selected, just fetch the aircraft for this buyer thru the buyer's belongsTo aircraft
                            if ($buyer && $get('contract_no')) {
                                return $buyer->lists->where('contract_no', $get('contract_no'))->pluck('style_no', 'style_no');
                            }

                            // no buyer selected, so get all aircraft
                            return PackingList::all()->pluck('style_no', 'style_no');
                        }),


                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['buyer_id'],
                            fn (Builder $query, $buyer): Builder => $query->whereHas('buyer', function (Builder $q) use ($buyer) {
                                $q->where('buyer_id', '=', $buyer);
                            }),
                        )
                        ->when(
                            $data['contract_no'],
                            fn (Builder $query, $type): Builder => $query->whereHas('buyer', function (Builder $query) use ($data) {
                                $query->where('contract_no', '=', $data['contract_no']);
                            })
                        )
                        ->when(
                            $data['style_no'],
                            fn (Builder $query, $type): Builder => $query->whereHas('buyer', function (Builder $query) use ($data) {
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
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
            Tables\Actions\ForceDeleteAction::make(),
            Tables\Actions\RestoreAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]),
        ]);
    }
    public static function getRelations(): array
    {
        return [
            PackingListResource\RelationsManager\CartonBoxesRelationManager::class,
            PackingListResource\RelationsManager\PackingListAttributesRelationManager::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => PackingListResource\Pages\ListPackingLists::route('/'),
            'create' => PackingListResource\Pages\CreatePackingList::route('/create'),
            'view' => PackingListResource\Pages\ViewPackingList::route('/{record}'),
            'edit' => PackingListResource\Pages\EditPackingList::route('/{record}/edit'),
        ];
    }
}
