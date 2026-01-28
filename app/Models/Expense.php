<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Expense extends Model
{
    //
    protected $fillable = [
        'expense_category_id',
        'account_id',
        'amount',
        'date',
        'description',
        'is_recurring',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'is_recurring' => 'boolean',
    ];

    // Relaciones
    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    // Scopes
    public function scopeCurrentMonth(Builder $query): Builder
    {
        return $query->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month);
    }

    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('expense_category_id', $categoryId);
    }

    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeRecurring(Builder $query): Builder
    {
        return $query->where('is_recurring', true);
    }
}
