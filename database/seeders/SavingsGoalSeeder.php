<?php

namespace Database\Seeders;

use App\Models\SavingsGoal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SavingsGoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $goals = [
            [
                'name' => 'Fondo de Emergencia',
                'target_amount' => 5000.00,
                'current_amount' => 2800.00,
                'deadline' => now()->addMonths(6),
                'status' => 'active',
                'color' => '#EF4444',
                'icon' => 'heroicon-o-shield-check',
                'description' => 'Fondo para emergencias equivalente a 3 meses de gastos',
            ],
            [
                'name' => 'Vacaciones 2026',
                'target_amount' => 2000.00,
                'current_amount' => 800.00,
                'deadline' => now()->addMonths(8),
                'status' => 'active',
                'color' => '#F59E0B',
                'icon' => 'heroicon-o-sun',
                'description' => 'Viaje de vacaciones a la playa',
            ],
            [
                'name' => 'Nueva Laptop',
                'target_amount' => 1500.00,
                'current_amount' => 450.00,
                'deadline' => now()->addMonths(4),
                'status' => 'active',
                'color' => '#3B82F6',
                'icon' => 'heroicon-o-computer-desktop',
                'description' => 'Comprar laptop para trabajo',
            ],
            [
                'name' => 'Curso de Especialización',
                'target_amount' => 800.00,
                'current_amount' => 800.00,
                'deadline' => now()->subMonths(1),
                'status' => 'completed',
                'color' => '#10B981',
                'icon' => 'heroicon-o-academic-cap',
                'description' => 'Curso online de desarrollo',
            ],
        ];

        foreach ($goals as $goal) {
            SavingsGoal::create($goal);
        }
    }
}
