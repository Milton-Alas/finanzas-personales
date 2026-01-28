<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Database;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomeSource extends Model
{
    //
    protected $fillable = [
        'name',
        'type',
        'expected_amount',
        'frequency',
        'is_active',
        'color',
        'description',
    ];

    protected $casts = [
        'expected_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    // Métodos auxiliares
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'fixed' => 'Fijo',
            'variable' => 'Variable',
        };
    }

    public function getFrequencyLabel(): string
    {
        return match($this->frequency) {
            'monthly' => 'Mensual',
            'irregular' => 'Irregular',
        };
    }

    public function getTotalCurrentMonth(): float
    {
        return $this->incomes()
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('amount');
    }
}
