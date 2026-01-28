<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingsGoal extends Model
{
    //
    protected $fillable = [
        'name',
        'target_amount',
        'current_amount',
        'deadline',
        'status',
        'color',
        'icon',
        'description',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'deadline' => 'date',
    ];

    // Relaciones
    public function savings(): HasMany
    {
        return $this->hasMany(Saving::class);
    }

    // Métodos auxiliares
    public function getProgressPercentage(): float
    {
        if ($this->target_amount == 0) {
            return 0;
        }

        return min(($this->current_amount / $this->target_amount) * 100, 100);
    }

    public function getRemainingAmount(): float
    {
        return max($this->target_amount - $this->current_amount, 0);
    }

    public function isCompleted(): bool
    {
        return $this->current_amount >= $this->target_amount;
    }

    public function getDaysRemaining(): ?int
    {
        if (!$this->deadline) {
            return null;
        }

        return now()->diffInDays($this->deadline, false);
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'active' => 'Activa',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
        };
    }

    public function addAmount(float $amount): void
    {
        $this->increment('current_amount', $amount);

        if ($this->isCompleted() && $this->status !== 'completed') {
            $this->update(['status' => 'completed']);
        }
    }
}
