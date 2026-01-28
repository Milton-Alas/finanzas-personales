<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\SavingsGoal;



class SavingsGoalsProgressWidget extends Widget
{
    protected string $view = 'filament.widgets.savings-goals-progress-widget';
    
    // Posición en el dashboard
    protected static ?int $sort = 4;
    
    // Ancho completo en el dashboard
    protected int | string | array $columnSpan = 1;

    /**
     * Pasamos los datos a la vista de Blade
     */
    protected function getViewData(): array
    {
        return [
            'goals' => SavingsGoal::where('status', 'active')
                ->orderBy('deadline', 'asc')
                ->get(),
        ];
    }
}
