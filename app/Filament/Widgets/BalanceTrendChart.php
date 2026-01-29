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

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            
            $months[] = $date->translatedFormat('F');

            $monthlyIncome = (float) Income::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $monthlyExpense = (float) Expense::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $monthlySavings = (float) Saving::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $balances[] = $monthlyIncome - $monthlyExpense - $monthlySavings;
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
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
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
}