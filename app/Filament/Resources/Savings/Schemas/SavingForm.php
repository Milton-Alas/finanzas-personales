<?php

namespace App\Filament\Resources\Savings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;


class SavingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->schema([
            Section::make('Información del Ahorro')
            ->components([
                Select::make('account_id')
                    ->label('Cuenta de Origen')
                    ->relationship('account', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->helperText('Cuenta desde la cual se hace el ahorro')
                    ->columnSpan(1),
                Select::make('savings_goal_id')
                    ->label('Meta de Ahorro')
                    ->relationship(
                        name: 'savingsGoal', 
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('status', 'active')
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->placeholder('Ninguna (ahorro general)')
                    ->helperText('Opcional: asociar a una meta específica')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->label('Nombre de la Meta')
                            ->maxLength(255),
                        TextInput::make('target_amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->label('Monto Objetivo')
                            ->step(0.01),
                    ])
                    ->columnSpan(1),
                TextInput::make('amount')
                    ->label('Monto a Ahorrar')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->minValue(0.01)
                    ->step(0.01)
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
                    ->placeholder('Opcional: detalles del ahorro')
                    ->columnSpanFull()
                    ->maxLength(65535),
                ])
                ->columns(2),
        ]);
    }
}
