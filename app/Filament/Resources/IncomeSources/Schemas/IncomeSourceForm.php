<?php

namespace App\Filament\Resources\IncomeSources\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class IncomeSourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->schema([
            Section::make('Información de la Fuente')
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ej: Salario, Freelance')
                    ->columnSpanFull(),
                Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'fixed' => 'Fijo',
                        'variable' => 'Variable',
                    ])
                    ->required()
                    ->native(false)
                    ->live()
                    ->columnSpan(1),
                TextInput::make('expected_amount')
                    ->label('Monto Esperado')
                    ->numeric()
                    ->prefix('$')
                    ->placeholder('Solo para ingresos fijos')
                    //->visible(fn (Forms\Get $get): bool => $get('type') === 'fixed')
                    ->visible(fn (Get $get): bool => $get('type') === 'fixed')
                    ->columnSpan(1),                  
                Select::make('frequency')
                    ->label('Frecuencia')
                    ->options([
                        'monthly' => 'Mensual',
                        'irregular' => 'Irregular',
                    ])
                    ->default('irregular')
                    ->required()
                    ->native(false)
                    ->columnSpan(1),
                Toggle::make('is_active')
                    ->label('Fuente Activa')
                    ->required()
                    ->default(true)
                    ->columnSpan(1),
                ColorPicker::make('color')
                    ->label('Color')
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
