<?php

namespace App\Observers;

use App\Models\Income;
use App\Models\Account;

class IncomeObserver
{
    /**
     * Handle the Income "created" event.
     */
    public function created(Income $income): void
    {
        // Aumentar el balance de la cuenta cuando se crea un ingreso
        $income->account->increment('balance', $income->amount);

    }

    /**
     * Handle the Income "updated" event.
     */
    public function updated(Income $income): void
    {
        if ($income->isDirty(['account_id', 'amount'])) {
            // Caso: Cambió la cuenta
            if ($income->isDirty('account_id')) {
                // 1. Revertir balance en cuenta vieja
                $oldAccountId = $income->getOriginal('account_id');
                $oldAccount = Account::find($oldAccountId);
                if ($oldAccount) {
                    $oldAccount->decrement('balance', $income->getOriginal('amount'));
                }
                
                // 2. Sumar en la cuenta nueva
                $income->account?->increment('balance', $income->amount);
            } 
            // Caso: Solo cambió el monto en la misma cuenta
            else {
                $oldAmount = $income->getOriginal('amount');
                $difference = $income->amount - $oldAmount;
                $income->account?->increment('balance', $difference);
            }
        }
    }

    /**
     * Handle the Income "deleted" event.
     */
    public function deleted(Income $income): void
    {
        // Disminuir el balance de la cuenta cuando se elimina un ingreso
        $income->account->decrement('balance', $income->amount);

    }

    /**
     * Handle the Income "restored" event.
     */
    public function restored(Income $income): void
    {
        // Si se restaura un ingreso eliminado, aumentar el balance
        $income->account->increment('balance', $income->amount);

    }

    /**
     * Handle the Income "force deleted" event.
     */
    public function forceDeleted(Income $income): void
    {
         // Mismo comportamiento que deleted
         $income->account->decrement('balance', $income->amount);

    }
}
