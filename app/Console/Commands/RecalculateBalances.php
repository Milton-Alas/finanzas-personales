<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Account;
use App\Models\SavingsGoal;
//use Illuminate\Console\Command;


class RecalculateBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balances:recalculate 
                            {--accounts : Recalcular solo balances de cuentas}
                            {--goals : Recalcular solo metas de ahorro}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcular balances de cuentas y metas de ahorro basándose en transacciones';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $recalculateAccounts = $this->option('accounts') || (!$this->option('accounts') && !$this->option('goals'));
        $recalculateGoals = $this->option('goals') || (!$this->option('accounts') && !$this->option('goals'));


        if ($recalculateAccounts) {
            $this->info('Recalculando balances de cuentas...');
            $this->recalculateAccountBalances();
        }


        if ($recalculateGoals) {
            $this->info('Recalculando metas de ahorro...');
            $this->recalculateSavingsGoals();
        }


        $this->info('✓ Proceso completado exitosamente');
    }


    /**
     * Recalcular balances de todas las cuentas
     */
    protected function recalculateAccountBalances(): void
    {
        $accounts = Account::all();
        $bar = $this->output->createProgressBar(count($accounts));
        $bar->start();


        foreach ($accounts as $account) {
            // Calcular balance real basado en transacciones
            $totalIncome = $account->incomes()->sum('amount');
            $totalExpenses = $account->expenses()->sum('amount');
            $totalSavings = $account->savings()->sum('amount');
            $transfersOut = $account->transfersFrom()->sum('amount');
            $transfersIn = $account->transfersTo()->sum('amount');
            
            $calculatedBalance = $totalIncome - $totalExpenses - $totalSavings - $transfersOut + $transfersIn;
            
            // Actualizar solo si hay diferencia
            if ($account->balance != $calculatedBalance) {
                $account->update(['balance' => $calculatedBalance]);
                $this->newLine();
                $this->line("  - {$account->name}: " . number_format($account->balance, 2) . " → " . number_format($calculatedBalance, 2));
            }
            
            $bar->advance();
        }


        $bar->finish();
        $this->newLine(2);
    }


    /**
     * Recalcular metas de ahorro
     */
    protected function recalculateSavingsGoals(): void
    {
        $goals = SavingsGoal::all();
        $bar = $this->output->createProgressBar(count($goals));
        $bar->start();


        foreach ($goals as $goal) {
            // Calcular monto real basado en ahorros
            $calculatedAmount = $goal->savings()->sum('amount');
            
            // Actualizar solo si hay diferencia
            if ($goal->current_amount != $calculatedAmount) {
                $goal->update(['current_amount' => $calculatedAmount]);
                $this->newLine();
                $this->line("  - {$goal->name}: " . number_format($goal->current_amount, 2) . " → " . number_format($calculatedAmount, 2));
            }
            
            // Actualizar estado si se completó
            if ($goal->isCompleted() && $goal->status !== 'completed') {
                $goal->update(['status' => 'completed']);
                $this->line("    ✓ Meta completada");
            }
            
            $bar->advance();
        }


        $bar->finish();
        $this->newLine(2);
    }

}
