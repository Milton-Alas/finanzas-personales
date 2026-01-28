<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Transfer extends Model
{
    //
    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'amount',
        'date',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    // Relaciones
    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transfer) {
            // 1. Validar que no sea la misma cuenta
            if ($transfer->from_account_id == $transfer->to_account_id) {
                throw ValidationException::withMessages([
                    'to_account_id' => 'La cuenta destino debe ser diferente a la cuenta origen.',
                ]);
            }

            // 2. Validar fondos suficientes usando la lógica de tu modelo Account
            $fromAccount = Account::find($transfer->from_account_id);
            
            if ($fromAccount && !$fromAccount->hasSufficientBalance($transfer->amount)) {
                throw ValidationException::withMessages([
                    'amount' => 'Fondos insuficientes en la cuenta origen para realizar esta transferencia.',
                ]);
            }
        });
    }
}
