<?php

namespace App\Observers;

use App\Models\Saving;
use App\Models\Account;
use Filament\Notifications\Notification;

class SavingObserver
{
    /**
     * Handle the Saving "created" event.
     */
    public function created(Saving $saving): void
    {
        /// Disminuir el balance de la cuenta (se saca dinero para ahorrar)
        $saving->account->decrement('balance', $saving->amount);
        
        // Si está asociado a una meta, actualizar el monto actual
        if ($saving->savingsGoal) {
            $saving->savingsGoal->increment('current_amount', $saving->amount);
            
            // Verificar si se completó la meta
            $this->checkGoalCompletion($saving->savingsGoal);
        }

    }

    /**
     * Handle the Saving "updated" event.
     */
    public function updated(Saving $saving): void
    {
        // Si cambió la cuenta, el monto o la meta, recalcular
        if ($saving->isDirty(['account_id', 'amount', 'savings_goal_id'])) {
            
            // Si cambió la cuenta
            if ($saving->isDirty('account_id')) {
                $oldAccountId = $saving->getOriginal('account_id');
                $oldAccount = \App\Models\Account::find($oldAccountId);
                if ($oldAccount) {
                    $oldAccount->increment('balance', $saving->getOriginal('amount'));
                }
                $saving->account->decrement('balance', $saving->amount);
            }
            // Si solo cambió el monto
            else if ($saving->isDirty('amount')) {
                $oldAmount = $saving->getOriginal('amount');
                $difference = $saving->amount - $oldAmount;
                $saving->account->decrement('balance', $difference);
            }
            
            // Manejar cambio de meta
            if ($saving->isDirty('savings_goal_id')) {
                $oldGoalId = $saving->getOriginal('savings_goal_id');
                $oldAmount = $saving->getOriginal('amount');
                
                // Restar de la meta anterior
                if ($oldGoalId) {
                    $oldGoal = \App\Models\SavingsGoal::find($oldGoalId);
                    if ($oldGoal) {
                        $oldGoal->decrement('current_amount', $oldAmount);
                    }
                }
                
                // Sumar a la nueva meta
                if ($saving->savingsGoal) {
                    $saving->savingsGoal->increment('current_amount', $saving->amount);
                    $this->checkGoalCompletion($saving->savingsGoal);
                }
            }
            // Si cambió el monto pero no la meta
            else if ($saving->isDirty('amount') && $saving->savingsGoal) {
                $oldAmount = $saving->getOriginal('amount');
                $difference = $saving->amount - $oldAmount;
                $saving->savingsGoal->increment('current_amount', $difference);
                $this->checkGoalCompletion($saving->savingsGoal);
            }
        }

    }

    /**
     * Handle the Saving "deleted" event.
     */
    public function deleted(Saving $saving): void
    {
        // Devolver el dinero a la cuenta
        $saving->account->increment('balance', $saving->amount);
        
        // Si está asociado a una meta, disminuir el monto actual
        if ($saving->savingsGoal) {
            $saving->savingsGoal->decrement('current_amount', $saving->amount);
        }

    }

    /**
     * Handle the Saving "restored" event.
     */
    public function restored(Saving $saving): void
    {
        // Volver a sacar el dinero de la cuenta
        $saving->account->decrement('balance', $saving->amount);
        
        // Volver a sumar a la meta
        if ($saving->savingsGoal) {
            $saving->savingsGoal->increment('current_amount', $saving->amount);
            $this->checkGoalCompletion($saving->savingsGoal);
        }

    }

    /**
     * Handle the Saving "force deleted" event.
     */
    public function forceDeleted(Saving $saving): void
    {
        // Mismo comportamiento que deleted
        $saving->account->increment('balance', $saving->amount);
        
        if ($saving->savingsGoal) {
            $saving->savingsGoal->decrement('current_amount', $saving->amount);
        }

    }

    /**
     * Verificar si se completó la meta de ahorro
     */
    protected function checkGoalCompletion($goal): void
    {
        if ($goal->isCompleted() && $goal->status !== 'completed') {
            $goal->update(['status' => 'completed']);
            
            Notification::make()
                ->success()
                ->title('¡Meta completada! 🎉')
                ->body("Has alcanzado tu meta de ahorro '{$goal->name}' de $" . number_format($goal->target_amount, 2) . ".")
                ->persistent()
                ->send();
        }
    }

}
