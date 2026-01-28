<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'balance',
        'currency',
        'is_active',
        'color',
        'icon',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'balance' => 'decimal:2',
    ];

    /**
     * Verifica si hay saldo suficiente.
     * Cambiamos float por mixed/numeric para evitar errores de precisión.
     */
    public function hasSufficientBalance($amount): bool
    {
        // Si es tarjeta de crédito, podrías tener un campo 'credit_limit'
        // Por ahora, asumimos que crédito siempre permite gasto.
        if ($this->type === 'credit_card') {
            return true; 
        }
        
        // Usamos bc_comp o simplemente la comparación directa que Laravel maneja bien con decimal casts
        return $this->balance >= $amount;
    }

    /**
     * Obtener el balance disponible.
     * En tarjetas de crédito, si el balance es negativo, significa que DEBES dinero.
     */
    public function getAvailableBalance(): float
    {
        // Si el balance de una cuenta normal es negativo por algún error,
        // devolvemos 0 para no confundir al usuario en la UI.
        return (float) max($this->balance, 0);
    }
    
    // RELACIONES (Importantes para que el comando de recalcular funcione)
    public function incomes(): HasMany { return $this->hasMany(Income::class); }
    public function expenses(): HasMany { return $this->hasMany(Expense::class); }
    public function savings(): HasMany { return $this->hasMany(Saving::class); }
    public function transfersFrom(): HasMany { return $this->hasMany(Transfer::class, 'from_account_id'); }
    public function transfersTo(): HasMany { return $this->hasMany(Transfer::class, 'to_account_id'); 
    }

}
