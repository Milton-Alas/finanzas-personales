<?php

namespace App\Filament\Resources\Transfers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;
use Filament\Forms\Set;


class TransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->schema([
            Section::make('Información de la Transferencia')
                ->schema([
                    Select::make('from_account_id')
                        ->label('Cuenta Origen')
                        ->relationship(
                            name: 'fromAccount', // Asegúrate de tener esta relación en tu modelo Transfer
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query->where('is_active', true)
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->live() // Permite que 'Cuenta Destino' reaccione al cambio
                        ->columnSpan(1),
        
                        Select::make('to_account_id')
                        ->label('Cuenta Destino')
                        ->relationship(
                            name: 'toAccount',
                            titleAttribute: 'name',
                            // Quitamos "Get" para que PHP no valide el origen de la clase
                            modifyQueryUsing: fn (Builder $query, $get) => $query
                                ->where('is_active', true)
                                ->where('id', '!=', $get('from_account_id'))
                        )
                        ->disabled(fn ($get): bool => !$get('from_account_id')) // También aquí
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->columnSpan(1),
        
                    TextInput::make('amount')
                        ->label('Monto a Transferir')
                        ->numeric()
                        ->prefix('$')
                        ->required()
                        ->minValue(0.01)
                        ->step(0.01) // Permite decimales correctamente
                        ->columnSpan(1),
        
                    DatePicker::make('date')
                        ->label('Fecha')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->default(now())
                        ->required()
                        ->maxDate(now())
                        ->closeOnDateSelection()
                        ->columnSpan(1),
        
                    Textarea::make('description')
                        ->label('Descripción')
                        ->rows(3)
                        ->placeholder('Opcional: motivo de la transferencia')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        
            Section::make('Información')
                ->schema([
                    Placeholder::make('info')
                        ->label('') // Ocultamos el label del placeholder para que solo se vea el texto
                        ->content('💡 Las transferencias mueven dinero entre tus cuentas. El balance de la cuenta origen disminuirá y el de la cuenta destino aumentará automáticamente.')
                        ->columnSpanFull(),
                ])
                ->collapsible(),
        ]);
        /*
            ->components([
                TextInput::make('from_account_id')
                    ->required()
                    ->numeric(),
                TextInput::make('to_account_id')
                    ->required()
                    ->numeric(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('date')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);*/
    }
}
