<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Account;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Saving;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        // Totales del mes actual (Usando los scopes que corregimos)
        $currentMonthIncome = (float) Income::currentMonth()->sum('amount');
        $currentMonthExpenses = (float) Expense::currentMonth()->sum('amount');
        $currentMonthSavings = (float) Saving::currentMonth()->sum('amount');
        
        // Totales del mes anterior
        $lastMonth = now()->subMonth();
        $lastMonthIncome = (float) Income::whereYear('date', $lastMonth->year)
            ->whereMonth('date', $lastMonth->month)
            ->sum('amount');
        
        $lastMonthExpenses = (float) Expense::whereYear('date', $lastMonth->year)
            ->whereMonth('date', $lastMonth->month)
            ->sum('amount');
        
        // Balance total y mensual
        $totalBalance = (float) Account::where('is_active', true)->sum('balance');
        $monthlyBalance = $currentMonthIncome - $currentMonthExpenses - $currentMonthSavings;
        
        // Cambios porcentuales
        $incomeChange = $lastMonthIncome > 0 ? (($currentMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100 : 0;
        $expenseChange = $lastMonthExpenses > 0 ? (($currentMonthExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100 : 0;

        return [
            Stat::make('Ingresos del Mes', '$' . number_format($currentMonthIncome, 2))
                ->description(abs(round($incomeChange, 1)) . '% ' . ($incomeChange >= 0 ? 'más' : 'menos') . ' que el mes pasado')
                ->descriptionIcon($incomeChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($incomeChange >= 0 ? 'success' : 'warning')
                ->chart($this->getIncomeChart()),
            
            Stat::make('Gastos del Mes', '$' . number_format($currentMonthExpenses, 2))
                ->description(abs(round($expenseChange, 1)) . '% ' . ($expenseChange >= 0 ? 'más' : 'menos') . ' que el mes pasado')
                ->descriptionIcon($expenseChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($expenseChange >= 0 ? 'danger' : 'success')
                ->chart($this->getExpenseChart()),
            
            Stat::make('Balance Total', '$' . number_format($totalBalance, 2))
                ->description('Fondos en cuentas activas')
                ->descriptionIcon('heroicon-m-wallet')
                ->color($totalBalance >= 0 ? 'info' : 'danger'),
        ];
    }

    protected function getIncomeChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $data[] = (float) Income::whereDate('date', now()->subDays($i)->toDateString())->sum('amount');
        }
        return $data;
    }

    protected function getExpenseChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $data[] = (float) Expense::whereDate('date', now()->subDays($i)->toDateString())->sum('amount');
        }
        return $data;
    }
}
