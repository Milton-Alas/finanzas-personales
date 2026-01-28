<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\Account;
use Filament\Notifications\Notification;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        // Disminuir el balance de la cuenta cuando se crea un gasto
        $expense->account?->decrement('balance', $expense->amount);
        
        // Verificar si se superó el presupuesto de la categoría
        $this->checkBudgetLimit($expense);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        // Si cambió la cuenta o el monto, recalcular
        if ($expense->isDirty(['account_id', 'amount'])) {
            // Si cambió la cuenta
            if ($expense->isDirty('account_id')) {
                // Sumar al balance de la cuenta anterior (devolver el dinero)
                $oldAccountId = $expense->getOriginal('account_id');
                $oldAccount = \App\Models\Account::find($oldAccountId);
                if ($oldAccount) {
                    $oldAccount->increment('balance', $expense->getOriginal('amount'));
                }
                
                // Restar del balance de la nueva cuenta
                $expense->account->decrement('balance', $expense->amount);
            } 
            // Si solo cambió el monto
            else if ($expense->isDirty('amount')) {
                $oldAmount = $expense->getOriginal('amount');
                $difference = $expense->amount - $oldAmount;
                
                // Si el nuevo monto es mayor, restar más
                // Si es menor, devolver dinero
                $expense->account->decrement('balance', $difference);
            }
        }
        
        // Verificar presupuesto después de actualizar
        if ($expense->isDirty(['amount', 'expense_category_id'])) {
            $this->checkBudgetLimit($expense);
        }

    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
         // Aumentar el balance de la cuenta cuando se elimina un gasto (devolver dinero)
         $expense->account->increment('balance', $expense->amount);

    }

    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
        // Si se restaura un gasto eliminado, disminuir el balance
        $expense->account->decrement('balance', $expense->amount);

    }

    /**
     * Handle the Expense "force deleted" event.
     */
    public function forceDeleted(Expense $expense): void
    {
        // Mismo comportamiento que deleted
        $expense->account->increment('balance', $expense->amount);

    }

    /**
     * Verificar si se superó el límite del presupuesto
     */
    protected function checkBudgetLimit(Expense $expense): void
    {
        $category = $expense->expenseCategory;
        
        // Solo verificar si la categoría tiene un límite de presupuesto
        if (!$category->budget_limit) {
            return;
        }
        
        // Si se superó el presupuesto, enviar notificación
        if ($category->isOverBudget()) {
            $spent = $category->getMonthlySpent();
            $percentage = $category->getBudgetPercentage();
            
            Notification::make()
                ->warning()
                ->title('Presupuesto superado')
                ->body("La categoría '{$category->name}' ha superado su presupuesto mensual. Has gastado $" . number_format($spent, 2) . " de $" . number_format($category->budget_limit, 2) . " (" . number_format($percentage, 1) . "%).")
                ->persistent()
                ->send();
        }
        // Advertencia al 80%
        else if ($category->getBudgetPercentage() >= 80) {
            $percentage = $category->getBudgetPercentage();
            
            Notification::make()
                ->warning()
                ->title('Advertencia de presupuesto')
                ->body("La categoría '{$category->name}' está al " . number_format($percentage, 1) . "% de su presupuesto mensual.")
                ->send();
        }
    }

}
