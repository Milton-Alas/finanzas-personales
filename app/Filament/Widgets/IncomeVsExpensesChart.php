<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Carbon;

class IncomeVsExpensesChart extends ChartWidget
{
    protected ?string $heading = 'Ingresos vs Gastos (Últimos 6 Meses)';

    protected static ?int $sort = 5;
    
    // Cambiado de 'full' a 1 para que ocupen media pantalla
    protected int | string | array $columnSpan = '1';

    protected function getData(): array
    {
        $months = [];
        $incomes = [];
        $expenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            
            // Usamos formatLocalized o translatedFormat para obtener el mes en español automáticamente
            $months[] = $date->translatedFormat('F');

            $incomes[] = (float) Income::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $expenses[] = (float) Expense::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ingresos',
                    'data' => $incomes,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => '#22c55e',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Gastos',
                    'data' => $expenses,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'borderColor' => '#ef4444',
                    'fill' => 'start',
                    'tension' => 0.4,
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
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
