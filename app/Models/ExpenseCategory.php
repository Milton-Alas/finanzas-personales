<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    //
    protected $fillable = [
        'name',
        'color',
        'icon',
        'budget_limit',
        'parent_id',
        'is_active',
        'description',
    ];

    protected $casts = [
        'budget_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ExpenseCategory::class, 'parent_id');
    }

    // Métodos auxiliares
    public function getMonthlySpent(int $year = null, int $month = null): float
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        return $this->expenses()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');
    }

    public function getBudgetPercentage(): ?float
    {
        if (!$this->budget_limit) {
            return null;
        }

        $spent = $this->getMonthlySpent();
        return ($spent / $this->budget_limit) * 100;
    }

    public function isOverBudget(): bool
    {
        if (!$this->budget_limit) {
            return false;
        }

        return $this->getMonthlySpent() > $this->budget_limit;
    }
}
