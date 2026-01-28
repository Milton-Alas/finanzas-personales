<?php

namespace App\Filament\Resources\SavingsGoals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

use Carbon\Carbon;

class SavingsGoalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información de la Meta')
                    ->components([
                        TextInput::make('name')
                            ->label('Nombre de la Meta')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Vacaciones 2026, Fondo de Emergencia')
                            ->columnSpanFull(),

                        TextInput::make('target_amount')
                            ->label('Monto Objetivo')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(0.01)
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateMonthlyAmount($set, $get))
                            ->columnSpan(1),

                        TextInput::make('current_amount')
                            ->label('Monto Actual')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Se actualiza automáticamente al agregar ahorros')
                            ->columnSpan(1),

                        DatePicker::make('deadline')
                            ->label('Fecha Límite')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Opcional')
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateMonthlyAmount($set, $get))
                            ->columnSpan(1),

                        Placeholder::make('monthly_amount_suggestion')
                            ->label('Ahorro Mensual Sugerido')
                            //->visible(fn (Get $get): bool => $get('type') === 'fixed')
                            ->content(fn (Get $get): string => self::getMonthlyAmountSuggestion($get))
                            ->columnSpan(1),

                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activa',
                                'completed' => 'Completada',
                                'cancelled' => 'Cancelada',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        ColorPicker::make('color')
                            ->label('Color')
                            ->columnSpan(1),

                        TextInput::make('icon')
                            ->label('Icono (Heroicon)')
                            ->placeholder('heroicon-o-banknotes')
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Lógica para refrescar cálculos si fuera necesario
     */
    protected static function calculateMonthlyAmount(Set $set, Get $get): void
    {
        // En este caso, el Placeholder se refresca solo por ser ->live() los dependientes
    }

    /**
     * Obtener sugerencia de ahorro mensual
     */
    protected static function getMonthlyAmountSuggestion(Get $get): string
    {
        $targetAmount = floatval($get('target_amount'));
        $currentAmount = floatval($get('current_amount') ?? 0);
        $deadline = $get('deadline');

        if (!$targetAmount || !$deadline) {
            return 'Ingresa el monto objetivo y la fecha límite para ver la sugerencia';
        }

        $remaining = $targetAmount - $currentAmount;
        
        if ($remaining <= 0) {
            return '✅ ¡Meta ya alcanzada!';
        }

        $now = now();
        $deadlineDate = Carbon::parse($deadline);
        
        if ($deadlineDate->isPast()) {
            return '⚠️ La fecha límite ya pasó';
        }

        $monthsRemaining = $now->diffInMonths($deadlineDate);
        
        // Si falta menos de un mes, calculamos sobre 1 para evitar división por cero
        if ($monthsRemaining <= 0) {
            $monthsRemaining = 1;
        }

        $monthlyAmount = $remaining / $monthsRemaining;

        return '💡 Necesitas ahorrar aproximadamente $' . number_format($monthlyAmount, 2) . ' por mes durante ' . $monthsRemaining . ' meses';
    }
}