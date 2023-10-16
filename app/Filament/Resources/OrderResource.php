<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\PtbrMoney;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Pedido';
    protected static ?string $pluralLabel = 'Pedidos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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

                DatePicker::make('pickup')
                    ->translateLabel(),

                DatePicker::make('delivery')
                    ->translateLabel(),

                PtbrMoney::make('deposit')
                    ->live(onBlur: true)
                    ->translateLabel(),

                PtbrMoney::make('discount')
                    ->live(onBlur: true)
                    ->translateLabel(),

                PtbrMoney::make('amount')
                    ->translateLabel()
                    ->disabled(),

                Placeholder::make('totalToPay')
                    ->label('Total a pagar')
                    ->content(function (Get $get) {
                        $amount = ptbr_money_to_float($get('amount') ?? 0);
                        $deposit = ptbr_money_to_float($get('deposit') ?? 0);
                        $discount = ptbr_money_to_float($get('discount') ?? 0);

                        $totalToPay = $amount - $deposit - $discount;

                        return number_format($totalToPay, 2, ',', '.');
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->translateLabel()
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer.phone')
                    ->label('Telefone')
                    ->formatStateUsing(fn (string $state): string => format_phone($state))
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->translateLabel()
                    ->searchable(),

                Tables\Columns\TextColumn::make('pickup')
                    ->translateLabel()
                    ->date()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('delivery')
                    ->translateLabel()
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('pickup', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
