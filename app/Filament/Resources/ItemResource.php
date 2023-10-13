<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\PtbrMoney;
use App\Filament\Resources\ItemResource\Pages;
use App\Models\Item;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Items';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([])->schema([
                    TextInput::make('name')
                        ->label('Nome')
                        ->required(),

                    TextInput::make('description')
                        ->label('Descrição'),
                ])->columns(2),

                Grid::make([])->schema([
                    PtbrMoney::make('value')
                        ->label('Valor'),

                    PtbrMoney::make('value_repo')
                        ->label('Valor de reposição'),

                    TextInput::make('quantity')
                        ->label('Quantidade')
                        ->required()
                        ->numeric()
                        ->default(0),
                ])->columns(3),

                Grid::make([])->schema([
                    TextInput::make('width')
                        ->label('Largura (cm)')
                        ->numeric(),
                    TextInput::make('height')
                        ->label('Altura (cm)')
                        ->numeric(),
                    TextInput::make('length')
                        ->label('Comprimento (cm)')
                        ->numeric(),
                ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Valor')
                    ->formatStateUsing(fn ($state) => number_format($state, 2, ',', '.')),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantidade')
                    ->numeric(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
