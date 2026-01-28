<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\Saving;
use Illuminate\Support\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section; // Importante: v4 usa Schemas\Components
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Input;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;






class Reports extends Page
{
    protected string $view = 'filament.pages.reports';

    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?string $title = 'Reportes Financieros';
    protected static string|UnitEnum|null $navigationGroup = 'Gestión';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'end_date' => now()->endOfMonth()->format('Y-m-d'),
            'account_id' => null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make('Filtros de Reporte')
                    ->components([
                        DatePicker::make('start_date')
                            ->label('Fecha Inicio')
                            ->required()
                            ->live()
                            ->native(false),

                        DatePicker::make('end_date')
                            ->label('Fecha Fin')
                            ->required()
                            ->live()
                            ->native(false),

                        Select::make('account_id')
                            ->label('Cuenta (Opcional)')
                            ->options(Account::where('is_active', true)->pluck('name', 'id'))
                            ->placeholder('Todas las cuentas')
                            ->searchable()
                            ->live()
                            ->native(false),
                    ])
                    ->columns(3),
            ]);
    }

    public function getReportData(): array
    {
        // Extraemos valores del estado del formulario
        $startDate = $this->data['start_date'] ?? now()->startOfMonth();
        $endDate = $this->data['end_date'] ?? now()->endOfMonth();
        $accountId = $this->data['account_id'] ?? null;

        $incomesQuery = Income::whereBetween('date', [$startDate, $endDate]);
        $expensesQuery = Expense::whereBetween('date', [$startDate, $endDate]);
        $savingsQuery = Saving::whereBetween('date', [$startDate, $endDate]);

        if ($accountId) {
            $incomesQuery->where('account_id', $accountId);
            $expensesQuery->where('account_id', $accountId);
            $savingsQuery->where('account_id', $accountId);
        }

        $totalIncome = $incomesQuery->sum('amount');
        $totalExpenses = $expensesQuery->sum('amount');
        $totalSavings = $savingsQuery->sum('amount');

        // Gastos por categoría con filtrado eficiente
        $expensesByCategory = ExpenseCategory::with(['expenses' => function ($q) use ($startDate, $endDate, $accountId) {
            $q->whereBetween('date', [$startDate, $endDate]);
            if ($accountId) $q->where('account_id', $accountId);
        }])
        ->get()
        ->map(fn($cat) => [
            'name' => $cat->name,
            'total' => $cat->expenses->sum('amount'),
            'color' => $cat->color ?? '#64748b',
        ])
        ->filter(fn($item) => $item['total'] > 0)
        ->sortByDesc('total');

        return [
            'totals' => [
                'income' => $totalIncome,
                'expenses' => $totalExpenses,
                'savings' => $totalSavings,
                'balance' => $totalIncome - $totalExpenses - $totalSavings,
            ],
            'expensesByCategory' => $expensesByCategory,
            'topExpenses' => Expense::with(['expenseCategory', 'account'])
                ->whereBetween('date', [$startDate, $endDate])
                ->when($accountId, fn($q) => $q->where('account_id', $accountId))
                ->orderByDesc('amount')->take(5)->get(),
        ];
    }
}
