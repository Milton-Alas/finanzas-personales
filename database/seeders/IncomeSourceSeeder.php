<?php

namespace Database\Seeders;

use App\Models\IncomeSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncomeSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $sources = [
            [
                'name' => 'Salario',
                'type' => 'fixed',
                'expected_amount' => 2500.00,
                'frequency' => 'monthly',
                'is_active' => true,
                'color' => '#3B82F6',
                'description' => 'Salario mensual del trabajo principal',
            ],
            [
                'name' => 'Freelance',
                'type' => 'variable',
                'expected_amount' => null,
                'frequency' => 'irregular',
                'is_active' => true,
                'color' => '#F59E0B',
                'description' => 'Trabajos freelance ocasionales',
            ],
            [
                'name' => 'Inversiones',
                'type' => 'variable',
                'expected_amount' => null,
                'frequency' => 'irregular',
                'is_active' => true,
                'color' => '#10B981',
                'description' => 'Rendimientos de inversiones',
            ],
        ];

        foreach ($sources as $source) {
            IncomeSource::create($source);
        }
    }
}
