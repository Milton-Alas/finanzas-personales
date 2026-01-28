<?php

namespace App\Observers;

use App\Models\Transfer;

class TransferObserver
{
    /**
     * Handle the Transfer "created" event.
     */
    public function created(Transfer $transfer): void
    {
        // Disminuir balance de la cuenta origen
        $transfer->fromAccount->decrement('balance', $transfer->amount);
        
        // Aumentar balance de la cuenta destino
        $transfer->toAccount->increment('balance', $transfer->amount);

    }

    /**
     * Handle the Transfer "updated" event.
     */
    public function updated(Transfer $transfer): void
    {
        // Si cambió algún campo relevante, recalcular
        if ($transfer->isDirty(['from_account_id', 'to_account_id', 'amount'])) {
            
            $oldAmount = $transfer->getOriginal('amount');
            $oldFromAccountId = $transfer->getOriginal('from_account_id');
            $oldToAccountId = $transfer->getOriginal('to_account_id');
            
            // Devolver dinero a la cuenta origen anterior
            if ($oldFromAccountId) {
                $oldFromAccount = \App\Models\Account::find($oldFromAccountId);
                if ($oldFromAccount) {
                    $oldFromAccount->increment('balance', $oldAmount);
                }
            }
            
            // Quitar dinero de la cuenta destino anterior
            if ($oldToAccountId) {
                $oldToAccount = \App\Models\Account::find($oldToAccountId);
                if ($oldToAccount) {
                    $oldToAccount->decrement('balance', $oldAmount);
                }
            }
            
            // Aplicar nueva transferencia
            $transfer->fromAccount->decrement('balance', $transfer->amount);
            $transfer->toAccount->increment('balance', $transfer->amount);
        }

    }

    /**
     * Handle the Transfer "deleted" event.
     */
    public function deleted(Transfer $transfer): void
    {
        // Devolver dinero a la cuenta origen
        $transfer->fromAccount->increment('balance', $transfer->amount);
        
        // Quitar dinero de la cuenta destino
        $transfer->toAccount->decrement('balance', $transfer->amount);

    }

    /**
     * Handle the Transfer "restored" event.
     */
    public function restored(Transfer $transfer): void
    {
        // Volver a aplicar la transferencia
        $transfer->fromAccount->decrement('balance', $transfer->amount);
        $transfer->toAccount->increment('balance', $transfer->amount);

    }

    /**
     * Handle the Transfer "force deleted" event.
     */
    public function forceDeleted(Transfer $transfer): void
    {
        // Mismo comportamiento que deleted
        $transfer->fromAccount->increment('balance', $transfer->amount);
        $transfer->toAccount->decrement('balance', $transfer->amount);

    }
}
