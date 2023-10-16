<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Filament\Forms\Components\PtbrMoney;
use App\Models\Item;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([])->schema([
                    PtbrMoney::make('price')
                        ->label('Preço'),

                    PtbrMoney::make('price_repo')
                        ->label('Preço de reposição'),

                    TextInput::make('quantity')
                        ->label('Quantidade')
                        ->required()
                        ->numeric(),
                ])->columns(3),


            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->formatStateUsing(fn ($state) => number_format($state, 2, ',', '.')),
                Tables\Columns\TextColumn::make('price_repo')
                    ->label('Preço de reposição')
                    ->formatStateUsing(fn ($state) => number_format($state, 2, ',', '.')),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantidade')
                    ->numeric(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->form(function (AttachAction $action): array {
                        return [
                            $action->getRecordSelect()
                                ->live()
                                ->afterStateUpdated(
                                    function (callable $set, $state) {
                                        $item = Item::find($state);
                                        $set('quantity', $item?->stock ?? 1);
                                        $set('price', $item?->value ?? 1);
                                        $set('price_repo', $item?->value_repo ?? 1);
                                    }
                                ),
                            Grid::make([])->schema([
                                PtbrMoney::make('price')
                                    ->default(0),

                                TextInput::make('quantity')->numeric()->default(1),

                                PtbrMoney::make('price_repo')->default(0),
                            ])->columns(3),
                        ];
                    }),
            ])
            ->actions([
                EditAction::make()->label(''),
                DetachAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false);
    }
}
