<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\Traits\OrderForm;
use App\Models\Order;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    use OrderForm;

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Pedido';
    protected static ?string $pluralLabel = 'Pedidos';


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
        return [];
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
