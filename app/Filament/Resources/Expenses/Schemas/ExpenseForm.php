<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Schema;


class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->schema([
            Section::make('Información del Gasto')
            ->description('Ingresa los detalles principales del egreso.')
            ->components([
                Select::make('expense_category_id')
                    ->label('Categoría')
                    ->relationship('expenseCategory', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->label('Nombre de la Categoría')
                            ->maxLength(255), // Buena práctica añadir límites
                        ColorPicker::make('color')
                            ->label('Color'),
                    ])
                    ->columnSpan(1),
                Select::make('account_id')
                    ->label('Cuenta')
                    ->relationship('account', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpan(1),
                TextInput::make('amount')
                    ->label('Monto')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->minValue(0.01)
                    ->step(0.01) // Asegura que acepte decimales en el input HTML
                    ->columnSpan(1),
                DatePicker::make('date')
                    ->label('Fecha')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->default(now())
                    ->required()
                    ->maxDate(now())
                    ->closeOnDateSelection() // Mejora la UX al cerrar el calendario al elegir
                    ->columnSpan(1),
                Toggle::make('is_recurring')
                    ->label('Gasto Recurrente')
                    ->helperText('Marca si este gasto se repite mensualmente')
                    ->onColor('success') // Opcional: da color al estado activo
                    ->columnSpan(1),
                Textarea::make('description')
                    ->label('Descripción')
                    ->required()
                    ->rows(3)
                    ->placeholder('Describe el gasto...')
                    ->columnSpanFull()
                    ->maxLength(65535),
                ])
                ->columns(2),
          ]);
    }
}
