<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Income;
use App\Models\IncomeSource;
use App\Models\SavingsGoal;
use App\Models\Saving;
use Filament\Forms\Concerns\InteractsWithForms;

class MonthlyBudget extends Page
{   
    use InteractsWithForms;

    protected string $view = 'filament.pages.monthly-budget';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationLabel = 'Presupuesto Mensual';
    protected static ?string $title = 'Presupuesto Mensual';
    protected static string|UnitEnum|null $navigationGroup = 'Gestión';
    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'month' => now()->month,
            'year' => now()->year,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make('Filtros de Período')
                    ->components([
                        Select::make('month')
                            ->label('Mes')
                            ->options([
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),

                        Select::make('year')
                            ->label('Año')
                            ->options(function () {
                                $currentYear = now()->year;
                                $years = [];
                                for ($i = $currentYear - 3; $i <= $currentYear; $i++) {
                                    $years[$i] = $i;
                                }
                                return $years;
                            })
                            ->required()
                            ->native(false)
                            ->live(),
                    ])
                    ->columns(2),
            ]);
    }

    public function getBudgetData(): array
    {
        $month = $this->data['month'] ?? now()->month;
        $year = $this->data['year'] ?? now()->year;

        $categories = ExpenseCategory::where('is_active', true)
            ->whereNotNull('budget_limit')
            ->with(['expenses' => function ($query) use ($year, $month) {
                $query->whereYear('date', $year)
                      ->whereMonth('date', $month);
            }])
            ->get()
            ->map(function ($category) {
                $spent = $category->expenses->sum('amount');
                $budget = $category->budget_limit;
                $remaining = max($budget - $spent, 0);
                $percentage = $budget > 0 ? ($spent / $budget) * 100 : 0;
                
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'budget' => $budget,
                    'spent' => $spent,
                    'remaining' => $remaining,
                    'percentage' => $percentage,
                    'color' => $category->color ?? '#64748b',
                    'status' => $percentage >= 100 ? 'over' : ($percentage >= 80 ? 'warning' : 'ok'),
                ];
            })
            ->sortByDesc('percentage');

        $totalBudget = $categories->sum('budget');
        $totalSpent = $categories->sum('spent');

        return [
            'categories' => $categories,
            'totals' => [
                'budget' => $totalBudget,
                'spent' => $totalSpent,
                'remaining' => max($totalBudget - $totalSpent, 0),
                'percentage' => $totalBudget > 0 ? ($totalSpent / $totalBudget) * 100 : 0,
            ],
        ];
    }
}
