<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\DB;

class ExpensesByCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Gastos por Categoría (Mes Actual)';
       
    protected static ?int $sort = 3;
    
    // Ocupa la mitad de la pantalla para que quepa otro widget al lado
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        // Traemos las categorías y sumamos sus gastos del mes actual en una sola consulta
        $categories = ExpenseCategory::where('is_active', true)
            ->withSum(['expenses' => function ($query) {
                $query->currentMonth();
            }], 'amount')
            ->get()
            ->filter(fn ($category) => $category->expenses_sum_amount > 0)
            ->sortByDesc('expenses_sum_amount')
            ->take(10);

        return [
            'datasets' => [
                [
                    'label' => 'Gastos',
                    'data' => $categories->pluck('expenses_sum_amount')->map(fn($amt) => (float) $amt)->toArray(),
                    // Si no tienes colores en la DB, Filament usará colores por defecto
                    'backgroundColor' => $categories->pluck('color')->map(fn($color) => $color ?? '#6366f1')->toArray(),
                ],
            ],
            'labels' => $categories->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
