<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Schema;


class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->schema([
            Section::make('Información de la Cuenta')
            ->components([
                TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->placeholder('Ej: Banco BAC, Efectivo')
                ->columnSpanFull(),
                Select::make('type')
                    ->label('Tipo de Cuenta')
                    ->options([
                        'bank' => 'Cuenta Bancaria',
                        'cash' => 'Efectivo',
                        'credit_card' => 'Tarjeta de Crédito',
                        'debit_card' => 'Tarjeta de Débito',
                    ]),
                TextInput::make('balance')
                    ->label('Balance Actual')
                    ->numeric()
                    ->prefix('$')
                    ->default(0)
                    ->required()
                    ->columnSpan(1),
                Select::make('currency')
                    ->label('Moneda')
                    ->options([
                        'USD' => 'USD - Dólar',
                        'EUR' => 'EUR - Euro',
                        'MXN' => 'MXN - Peso Mexicano',
                    ]),
                Toggle::make('is_active')
                    ->label('Cuenta Activa')
                    ->default(true)
                    ->columnSpan(1),
                ColorPicker::make('color')
                    ->label('Color')
                    ->placeholder('#3B82F6')
                    ->columnSpan(1),
                TextInput::make('icon')
                    ->label('Icono (Heroicon)')
                    ->placeholder('heroicon-o-building-library')
                    ->columnSpan(1),
                Textarea::make('description')
                        ->label('Descripción')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            ]);
}
}
