<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Alimentación',
                'color' => '#EF4444',
                'icon' => 'heroicon-o-shopping-cart',
                'budget_limit' => 400.00,
                'is_active' => true,
                'description' => 'Supermercado, restaurantes, comida',
            ],
            [
                'name' => 'Transporte',
                'color' => '#3B82F6',
                'icon' => 'heroicon-o-truck',
                'budget_limit' => 150.00,
                'is_active' => true,
                'description' => 'Gasolina, transporte público, Uber',
            ],
            [
                'name' => 'Vivienda',
                'color' => '#8B5CF6',
                'icon' => 'heroicon-o-home',
                'budget_limit' => 600.00,
                'is_active' => true,
                'description' => 'Renta, servicios, mantenimiento',
            ],
            [
                'name' => 'Servicios',
                'color' => '#F59E0B',
                'icon' => 'heroicon-o-bolt',
                'budget_limit' => 200.00,
                'is_active' => true,
                'description' => 'Luz, agua, internet, teléfono',
            ],
            [
                'name' => 'Entretenimiento',
                'color' => '#EC4899',
                'icon' => 'heroicon-o-film',
                'budget_limit' => 100.00,
                'is_active' => true,
                'description' => 'Cine, streaming, salidas',
            ],
            [
                'name' => 'Salud',
                'color' => '#10B981',
                'icon' => 'heroicon-o-heart',
                'budget_limit' => 150.00,
                'is_active' => true,
                'description' => 'Médico, medicinas, gimnasio',
            ],
            [
                'name' => 'Educación',
                'color' => '#6366F1',
                'icon' => 'heroicon-o-academic-cap',
                'budget_limit' => 100.00,
                'is_active' => true,
                'description' => 'Cursos, libros, formación',
            ],
            [
                'name' => 'Ropa',
                'color' => '#14B8A6',
                'icon' => 'heroicon-o-shopping-bag',
                'budget_limit' => 80.00,
                'is_active' => true,
                'description' => 'Ropa, zapatos, accesorios',
            ],
            [
                'name' => 'Tecnología',
                'color' => '#64748B',
                'icon' => 'heroicon-o-device-phone-mobile',
                'budget_limit' => 100.00,
                'is_active' => true,
                'description' => 'Gadgets, software, suscripciones',
            ],
            [
                'name' => 'Mascotas',
                'color' => '#F97316',
                'icon' => 'heroicon-o-heart',
                'budget_limit' => 50.00,
                'is_active' => true,
                'description' => 'Comida, veterinario, accesorios',
            ],
            [
                'name' => 'Regalos',
                'color' => '#A855F7',
                'icon' => 'heroicon-o-gift',
                'budget_limit' => 80.00,
                'is_active' => true,
                'description' => 'Cumpleaños, celebraciones',
            ],
            [
                'name' => 'Otros',
                'color' => '#9CA3AF',
                'icon' => 'heroicon-o-ellipsis-horizontal',
                'budget_limit' => null,
                'is_active' => true,
                'description' => 'Gastos varios no categorizados',
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['name' => $category['name']],
                $category,
            );
        }
    }
}
