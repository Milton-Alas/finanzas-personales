<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// 2. IMPORTA TUS OBSERVERS
use App\Observers\IncomeObserver;
use App\Observers\ExpenseObserver;
use App\Observers\TransferObserver;
use App\Observers\SavingObserver;

// 1. IMPORTA TUS MODELOS
use App\Models\Income;
use App\Models\Expense;
use App\Models\Transfer;
use App\Models\Saving;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Income::observe(IncomeObserver::class);
        Expense::observe(ExpenseObserver::class);
        Transfer::observe(TransferObserver::class);
        Saving::observe(SavingObserver::class);
        
    }
}
