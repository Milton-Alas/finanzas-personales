<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Saving;
use Filament\Support\RawJs;

class BalanceTrendChart extends ChartWidget
{
    protected ?string $heading = 'Tendencia de Balance Mensual';

    protected int | string | array $columnSpan = '1';

    protected static ?int $sort = 6;
    

    protected function getData(): array
    {
        $months = [];
        $balances = [];

        // Obtener datos de los últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            
            $monthName = $this->getSpanishMonth($date->format('F'));
            $months[] = $monthName;

            // Calcular balance: Ingresos - Gastos - Ahorros
            $monthlyIncome = Income::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount') ?? 0;

            $monthlyExpense = Expense::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount') ?? 0;

            $monthlySavings = Saving::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount') ?? 0;

            $balance = $monthlyIncome - $monthlyExpense - $monthlySavings;
            
            // Forzar a 0 si es null
            $balances[] = $balance ?? 0;
            
            // Debug (puedes comentar esto después)
            // \Log::info("Mes: {$monthName}, Ingresos: {$monthlyIncome}, Gastos: {$monthlyExpense}, Ahorros: {$monthlySavings}, Balance: {$balance}");
        }

        return [
            'datasets' => [
                [
                    'label' => 'Balance',
                    'data' => $balances,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 5,
                    'pointBackgroundColor' => 'rgb(59, 130, 246)',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointHoverRadius' => 7,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'callbacks' => [
                        'label' => RawJs::make(<<<'JS'
                            function(context) {
                                let label = 'Balance: ';
                                if (context.parsed.y >= 0) {
                                    label += '+';
                                }
                                label += '$' + context.parsed.y.toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                                return label;
                            }
                        JS),
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true, // Importante: empezar desde 0
                    'ticks' => [
                        'callback' => RawJs::make(<<<'JS'
                            function(value) {
                                return '$' + value.toLocaleString('en-US', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                            }
                        JS),
                    ],
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.05)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }

    protected function getSpanishMonth(string $month): string
    {
        return [
            'January' => 'Ene', 
            'February' => 'Feb', 
            'March' => 'Mar',
            'April' => 'Abr', 
            'May' => 'May', 
            'June' => 'Jun',
            'July' => 'Jul', 
            'August' => 'Ago', 
            'September' => 'Sep',
            'October' => 'Oct', 
            'November' => 'Nov', 
            'December' => 'Dic',
        ][$month] ?? $month;
    }
}