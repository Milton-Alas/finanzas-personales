<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;


class Saving extends Model
{
    //
    protected $fillable = [
        'account_id',
        'savings_goal_id',
        'amount',
        'date',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    // Relaciones
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function savingsGoal(): BelongsTo
    {
        return $this->belongsTo(SavingsGoal::class);
    }

    // Scopes
    public function scopeCurrentMonth(Builder $query): Builder
    {
        return $query->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month);
    }

    public function scopeGeneral(Builder $query): Builder
    {
        return $query->whereNull('savings_goal_id');
    }

    public function scopeByGoal(Builder $query, int $goalId): Builder
    {
        return $query->where('savings_goal_id', $goalId);
    }

    public function isGeneral(): bool
    {
        return is_null($this->savings_goal_id);
    }
}
