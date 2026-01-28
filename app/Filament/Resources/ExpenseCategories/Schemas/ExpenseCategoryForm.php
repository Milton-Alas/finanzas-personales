<?php

namespace App\Filament\Resources\ExpenseCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Schema;

class ExpenseCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->schema([
            Section::make('Información de la Categoría')
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ej: Alimentación, Transporte')
                    ->columnSpanFull(),
                TextInput::make('budget_limit')
                    ->label('Presupuesto Mensual')
                    ->numeric()
                    ->prefix('$')
                    ->placeholder('Opcional')
                    ->helperText('Límite de gasto mensual para esta categoría')
                    ->columnSpan(1),
                ColorPicker::make('color')
                    ->label('Color')
                    ->columnSpan(1),
                TextInput::make('icon')
                    ->label('Icono (Heroicon)')
                    ->placeholder('heroicon-o-shopping-cart')
                    ->columnSpan(1),
                Select::make('parent_id')
                    ->label('Categoría Padre (Opcional)')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->placeholder('Ninguna (categoría principal)')
                    ->columnSpan(1),
                Toggle::make('is_active')
                    ->label('Categoría Activa')
                    ->default(true)
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
