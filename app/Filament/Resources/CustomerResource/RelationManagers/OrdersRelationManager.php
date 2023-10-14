<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Filament\Forms\Components\PtbrMoney;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->translateLabel(),

                DatePicker::make('pickup')
                    ->translateLabel(),

                DatePicker::make('delivery')
                    ->translateLabel(),

                PtbrMoney::make('deposit')
                    ->translateLabel(),

                PtbrMoney::make('discount')
                    ->translateLabel(),

                PtbrMoney::make('amount')
                    ->translateLabel()
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('pickup')
                    ->translateLabel()
                    ->date(),

                Tables\Columns\TextColumn::make('delivery')
                    ->translateLabel()
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('pickup', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->simplePaginate($this->getTableRecordsPerPage());
    }
}
