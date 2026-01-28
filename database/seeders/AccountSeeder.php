<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $accounts = [
            [
                'name' => 'Banco BAC',
                'type' => 'bank',
                'balance' => 5000.00,
                'currency' => 'USD',
                'is_active' => true,
                'color' => '#3B82F6',
                'icon' => 'heroicon-o-building-library',
                'description' => 'Cuenta de ahorros principal',
            ],
            [
                'name' => 'Efectivo',
                'type' => 'cash',
                'balance' => 300.00,
                'currency' => 'USD',
                'is_active' => true,
                'color' => '#10B981',
                'icon' => 'heroicon-o-banknotes',
                'description' => 'Dinero en efectivo',
            ],
            [
                'name' => 'Tarjeta Visa',
                'type' => 'credit_card',
                'balance' => -1200.00,
                'currency' => 'USD',
                'is_active' => true,
                'color' => '#EF4444',
                'icon' => 'heroicon-o-credit-card',
                'description' => 'Tarjeta de crédito principal',
            ],
            [
                'name' => 'Tarjeta Débito',
                'type' => 'debit_card',
                'balance' => 800.00,
                'currency' => 'USD',
                'is_active' => true,
                'color' => '#8B5CF6',
                'icon' => 'heroicon-o-credit-card',
                'description' => 'Tarjeta de débito asociada a BAC',
            ],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}
