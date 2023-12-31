<?php

namespace App\Filament\Resources\OrderResource\Traits;

use App\Filament\Forms\Components\PtbrMoney;
use App\Models\Item;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;

trait OrderForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([])->schema([
                    TextInput::make('description')
                        ->translateLabel(),

                    Select::make('customer_id')
                        ->label('Cliente')
                        ->searchable(['name', 'phone'])
                        ->relationship(name: 'customer', titleAttribute: 'name')
                        ->getOptionLabelFromRecordUsing(
                            fn ($record) => "{$record->name}"
                            . (
                                $record->phone
                                ? ' - ' . format_phone($record->phone)
                                : ''
                            )
                        ),
                ])->columns(2),

                Grid::make([])->schema([
                    DatePicker::make('pickup')
                        ->live(onBlur: true)
                        ->translateLabel(),

                    DatePicker::make('delivery')
                        ->live(onBlur: true)
                        ->translateLabel(),
                ])->columns(2),

                Grid::make([])->schema(self::getRepeater())->columns(1),

                Grid::make([])->schema([
                    Hidden::make('amount'),

                    PtbrMoney::make('deposit')
                        ->live(onBlur: true)
                        ->translateLabel(),

                    PtbrMoney::make('discount')
                        ->live(onBlur: true)
                        ->translateLabel(),

                    Placeholder::make('total')
                        ->label('Total')
                        ->content(function (Get $get, Set $set) {
                            $amount = collect($get('items'))
                                ->sum(
                                    function ($item) {
                                        $priceState = data_get($item, 'price', '0,00');
                                        $quantityState = data_get($item, 'quantity', '0');
                                        $priceFloat = ptbr_money_to_float(
                                            $priceState
                                        );
                                        $calc = $priceFloat * $quantityState;

                                        return $calc;
                                    }
                                );

                            $totalToPay = $amount
                                - ptbr_money_to_float($get('deposit') ?? 0)
                                - ptbr_money_to_float($get('discount') ?? 0);

                            $set('amount', number_format($totalToPay, 2, '.', ''));

                            return number_format($totalToPay, 2, ',', '.');
                        }),
                ])->columns(3),
            ]);
    }

    private static function getRepeater(): array
    {
        return [
            Repeater::make('items')
                ->label('Itens')
                ->relationship()
                ->mutateRelationshipDataBeforeFillUsing(function (array $data, Get $get): array {
                    $item = Item::find(
                        data_get($data, 'item_id')
                    );

                    $stock = $item ?
                        $item->getAvailableStock(
                            $get('data.pickup', true),
                            $get('data.delivery', true),
                            $get('data.id', true)
                        )
                        : 0;
                    data_set($data, 'stock', $stock);

                    return $data;
                })
                ->schema([
                    Grid::make([])->schema([
                        Select::make('item_id')
                            ->label('Item')
                            ->relationship('item', 'name')
                            ->disableOptionWhen(function (Get $get, string $value): bool {
                                return collect($get('data.items', true))
                                    ->pluck('item_id')
                                    ->contains($value);
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(
                                function (callable $set, Get $get, $state) {
                                    $item = Item::find($state);

                                    $stock = $item->getAvailableStock(
                                        $get('data.pickup', true),
                                        $get('data.delivery', true),
                                        $get('data.id', true)
                                    );

                                    $set('quantity', 1);
                                    $set('price', $item?->value ?? 1);
                                    $set('price_repo', $item?->value_repo ?? 1);
                                    $set('stock', $stock);
                                }
                            ),

                        PtbrMoney::make('price')
                            ->required()
                            ->live(onBlur: true)
                            ->label('Preço'),

                        PtbrMoney::make('price_repo')
                            ->required()
                            ->label('Preço de reposição'),

                        TextInput::make('quantity')
                            ->label('Quantidade')
                            ->required()
                            ->live(onBlur: true)
                            ->numeric(),

                        Hidden::make('stock'),

                        Placeholder::make('no-stock')
                            ->content('Sem estoque!')
                            ->label('Atenção:')
                            ->hidden(
                                fn (Get $get) => $get('stock') - $get('quantity') > 0
                            ),

                    ])
                        ->columns(5)
                        ->hidden(fn (Get $get): bool => ! $get('data.pickup', true) || ! $get('data.delivery', true)),
                ]),
        ];
    }
}
