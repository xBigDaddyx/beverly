<?php

namespace Xbigdaddyx\Beverly\Filament\Resources\PackingListResource\RelationsManager;

use Awcodes\Shout\Components\Shout;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Xbigdaddyx\Beverly\Filament\Resources\CartonBoxResource;

class CartonBoxesRelationManager extends RelationManager
{
    protected static string $relationship = 'cartonBoxes';
    protected bool $allowsDuplicates = false;
    protected static ?string $title = 'Boxes';
    public function form(Form $form): Form
    {
        //return CartonBoxResource::form($form);
        return $form->schema([
            Shout::make('important')
                ->hiddenOn('create')
                ->visible(fn (Model $record): bool => $record->isLocked())
                ->columnSpan('full')
                ->icon('tabler-lock')
                ->content('This carton box is locked because its already completed!')
                ->type('warning'),
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
                        ->relationship('packingList', 'po', modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()))
                        ->required()
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "PO: {$record->po} - {$record->buyer->name} {$record->buyer->country} - {$record->style_no}"),
                    Forms\Components\TextInput::make('carton_number')
                        ->default(0)
                        ->label('Carton Number'),
                    // Forms\Components\TextInput::make('size')
                    //     ->hidden(fn (Get $get): bool => $get('type') === 'RATIO')
                    //     ->label('Size'),
                    // Forms\Components\TextInput::make('color')
                    //     ->hidden(fn (Get $get): bool => $get('type') === 'RATIO')
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

            Forms\Components\Repeater::make('attributes')
                ->relationship()
                ->addable(function (Get $get) {
                    if ($get('type') === 'SOLID') {
                        return false;
                    }
                    return true;
                })
                ->schema([
                    Forms\Components\Placeholder::make('type')
                        ->live()
                        ->content(function (Get $get) {
                            if ($get('type')) {
                                return $get('type');
                            }
                            return '-';
                        })
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('size')
                        ->helperText('Should be the size of attribute')
                        ->hint('Size Attribute')
                        ->hintIcon('tabler-ruler-measure')
                        ->hintColor('primary'),
                    Forms\Components\TextInput::make('tag')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                            if (str_contains($state, 'LPN')) {

                                $explode = explode('&', $state ?? '');
                                $result = str_replace('item_number=', '', $explode[1]);
                                return $set('tag', $result);
                            }
                        })
                        ->helperText('Should be the tag of attribute')
                        ->hint('Tag Attribute')
                        ->hintIcon('tabler-tag')
                        ->hintColor('primary'),
                    Forms\Components\TextInput::make('color')
                        ->helperText('Should be the name of attribute')
                        ->hint('Color Attribute')
                        ->hintIcon('tabler-color-swatch')
                        ->hintColor('primary'),
                    Forms\Components\TextInput::make('quantity')
                        ->required()
                        ->numeric()
                        ->helperText('Should be the quantities of attribute')
                        ->hint('Quantity Attribute')
                        ->hintIcon('tabler-calculator')
                        ->hintColor('primary'),
                ])->columns(2)->columnSpanFull(),


        ]);
        // return $form
        //     ->schema([

        //         Forms\Components\Section::make('General Information')
        //             ->schema([
        //                 Forms\Components\TextInput::make('box_code')
        //                     ->helperText('Should be carton box barcode.')
        //                     ->hint('Carton Box Barcode')
        //                     ->hintIcon('tabler-barcode')
        //                     ->hintColor('primary')
        //                     ->label('Box Code'),

        //                 Forms\Components\TextInput::make('carton_number')
        //                     ->numeric()
        //                     ->label('Carton Number')
        //                     ->helperText('Define your carton numbering.')
        //                     ->hint('Carton Box Numbering')
        //                     ->hintIcon('tabler-numbers')
        //                     ->hintColor('primary'),
        //                 Forms\Components\Select::make('type')
        //                     ->reactive()
        //                     ->options([
        //                         'SOLID' => 'SOLID',
        //                         'MIX' => 'MIX',
        //                         'RATIO' => 'RATIO',
        //                     ])
        //                     ->required()
        //                     // ->hidden(fn (RelationManager $livewire): bool => $livewire->ownerRecord->type === 'RATIO' || $livewire->ownerRecord->type ==='RATIO SET')
        //                     ->default('SOLID')
        //                     ->helperText('type of box items')
        //                     ->hint('Items Type')
        //                     ->hintIcon('tabler-Forms\Components\selector')
        //                     ->hintColor('primary')
        //                     ->label('Type'),
        //                 Forms\Components\TextInput::make('size')
        //                     ->reactive()
        //                     ->hidden(function (RelationManager $livewire, $state, callable $get, callable $set) {
        //                         $type = $get('type');
        //                         if ($type !== null) {
        //                             if ($type === 'SOLID') {
        //                                 return false;
        //                             }

        //                             return true;
        //                         } elseif ($livewire->ownerRecord->type === 'RATIO') {
        //                             return true;
        //                         }
        //                     })
        //                     ->helperText('Fill size attribute of box items, or keep it blank.')
        //                     ->hint('For SOLID only!')
        //                     ->hintIcon('tabler-ruler')
        //                     ->hintColor('danger')
        //                     ->label('Size'),
        //                 Forms\Components\TextInput::make('color')
        //                     ->hidden(function (RelationManager $livewire, $state, callable $get, callable $set) {
        //                         $type = $get('type');
        //                         if ($type !== null) {
        //                             if ($type === 'SOLID') {
        //                                 return false;
        //                             }

        //                             return true;
        //                         } elseif ($livewire->ownerRecord->type === 'RATIO') {
        //                             return true;
        //                         }
        //                     })
        //                     ->helperText('Fill color attribute of box items, or keep it blank.')
        //                     ->hint('For SOLID only!')
        //                     ->hintIcon('tabler-color-swatch')
        //                     ->hintColor('danger')
        //                     ->label('Color'),
        //                 Forms\Components\TextInput::make('quantity')
        //                     ->helperText('Fill quantity attribute of box items, or keep it blank.')
        //                     ->hint('Quantity Items')
        //                     ->hintIcon('tabler-calculator')
        //                     ->hintColor('primary')
        //                     ->required()
        //                     ->numeric()
        //                     ->label('Quantity'),

        //                 Forms\Components\Checkbox::make('is_completed')
        //                     ->label('Completed')
        //                     ->hiddenOn('create'),
        //             ])->columns(2),
        //     ]);
    }

    public function table(Table $table): Table
    {
        return CartonBoxResource::table($table)
            ->queryStringIdentifier('carton-box-relations')
            ->paginated([10, 25, 50, 100, 'all'])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        if ($livewire->ownerRecord->type === 'RATIO') {
                            $data['type'] = 'RATIO';
                        }

                        return $data;
                    }),
            ]);
        // return $table
        //     ->recordTitleAttribute('box_code')
        //     ->columns([

        //         Tables\Columns\TextColumn::make('id')
        //             ->label('Box ID'),
        //         Tables\Columns\TextColumn::make('box_code')
        //             ->label('Box Code'),
        //         Tables\Columns\TextColumn::make('carton_number')
        //             ->label('Carton Number'),
        //         Tables\Columns\TextColumn::make('carton_number')
        //             ->label('Carton Number'),
        //         Tables\Columns\TextColumn::make('size')
        //             ->hidden(fn (RelationManager $livewire): bool => $livewire->ownerRecord->type === 'RATIO' || $livewire->ownerRecord->type === 'RATIO SET')
        //             ->label('Size'),
        //         Tables\Columns\TextColumn::make('color')
        //             ->hidden(fn (RelationManager $livewire): bool => $livewire->ownerRecord->type === 'RATIO' || $livewire->ownerRecord->type === 'RATIO SET')
        //             ->label('Color'),
        //         Tables\Columns\TextColumn::make('quantity')
        //             ->label('Quantity'),
        //         Tables\Columns\TextColumn::make('type')
        //             ->label('Type'),
        //         Tables\Columns\TextColumn::make('description')
        //             ->hidden(fn (RelationManager $livewire): bool => $livewire->ownerRecord->type === 'RATIO' || $livewire->ownerRecord->type === 'RATIO SET')
        //             ->label('Box Info'),
        //         Tables\Columns\IconColumn::make('is_completed')
        //             ->boolean()
        //             ->trueIcon('tabler-clipboard-check')
        //             ->falseIcon('tabler-clipboard-x')
        //             ->label('Completed'),
        //     ])
        //     ->filters([
        //         //
        //     ])
        //     ->headerActions([
        //         Tables\Actions\CreateAction::make()
        //             ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
        //                 if ($livewire->ownerRecord->type === 'RATIO') {
        //                     $data['type'] = 'RATIO';
        //                 }

        //                 return $data;
        //             }),
        //     ])
        //     ->actions([
        //         Tables\Actions\EditAction::make(),
        //         Tables\Actions\DeleteAction::make(),
        //     ])
        //     ->bulkActions([
        //         Tables\Actions\BulkActionGroup::make([
        //             Tables\Actions\DeleteBulkAction::make(),
        //         ]),
        //     ]);
    }
}
