<?php

namespace App\Filament\Resources\Incomes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IncomeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->schema([
            Section::make('Información del Ingreso')
            ->components([
                Select::make('income_source_id')
                    ->relationship('incomeSource', 'name')
                    ->label('Fuente de Ingreso')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(),
                Select::make('account_id')
                    ->label('Cuenta de Destino')
                    ->relationship('account', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpan(1),
               /* TextInput::make('name')
                    ->required()
                    ->label('Nombre de la Fuente'),
                Select::make('type')
                        ->options([
                            'fixed' => 'Fijo',
                            'variable' => 'Variable',
                        ])
                        ->required()
                        ->native(false)
                        ->label('Tipo'),
                ])
                ->columnSpan(1),*/
                TextInput::make('amount')
                    ->label('Monto')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->minValue(0.01)
                    ->columnSpan(1),
                DatePicker::make('date')
                ->label('Fecha')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->default(now())
                        ->required()
                        ->maxDate(now())
                        ->columnSpan(1),
                Textarea::make('description')                    
                    ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Opcional: detalles del ingreso')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);

    }
}
