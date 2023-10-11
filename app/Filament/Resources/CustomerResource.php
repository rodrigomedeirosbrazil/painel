<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Clientes';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('name')
                        ->label('Nome')
                        ->required(),
                    TextInput::make('email')
                        ->email(),
                    TextInput::make('doc')
                        ->mask(RawJs::make(<<<'JS'
                            $input.replace(/\D/g, '').length >= 12 ? '99.999.999/9999-99' : '999.999.999-99'
                        JS))
                        ->label('Documento')
                        ->dehydrateStateUsing(fn (string $state): string => only_numbers($state)),
                    TextInput::make('phone')
                        ->mask(RawJs::make(<<<'JS'
                            $input.replace(/\D/g, '').length >= 11 ? '(99) 99999-9999': '(99) 9999-9999'
                        JS))
                        ->label('Telefone')
                        ->dehydrateStateUsing(fn (string $state): string => only_numbers($state)),
                ])->columns(2),
                Section::make([
                    TextInput::make('zipcode')
                        ->label('CEP')
                        ->mask('99.999-999')
                        ->suffixAction(
                            fn ($state, Set $set) => Action::make('search-action')
                                ->icon('heroicon-o-magnifying-glass')
                                ->action(function () use ($state, $set) {
                                    $cepData = static::getAddressData($state);

                                    $set('district', data_get($cepData, 'neighborhood'));
                                    $set('street', data_get($cepData, 'street'));
                                    $set('city', data_get($cepData, 'city'));
                                    $set('state', data_get($cepData, 'state'));
                                    $set('longitude', data_get($cepData, 'location.coordinates.longitude'));
                                    $set('latitude', data_get($cepData, 'location.coordinates.latitude'));
                                })
                        )
                        ->dehydrateStateUsing(fn (string $state): string => only_numbers($state)),
                    TextInput::make('city')
                        ->label('Cidade'),
                    TextInput::make('state')
                        ->label('Estado'),
                    TextInput::make('street')
                        ->label('Endereço'),
                    TextInput::make('number')
                        ->label('Número'),
                    TextInput::make('complement')
                        ->label('Complemento'),
                    TextInput::make('district')
                        ->label('Bairro'),
                ])
                    ->collapsible()
                    ->description('Endereço')
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('doc')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => format_doc($state)),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => format_phone($state)),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getAddressData(?string $cep): ?array
    {
        if (blank($cep)) {
            Filament::notify('danger', 'Digite o CEP para buscar o endereço');

            return null;
        }

        try {
            $cepData = Http::get("https:/brasilapi.com.br/api/cep/v2/{$cep}")
                ->throw()
                ->json();
        } catch (RequestException $e) {
            Filament::notify('danger', 'Erro ao buscar o endereço');

            return null;
        }

        return $cepData;
    }
}
