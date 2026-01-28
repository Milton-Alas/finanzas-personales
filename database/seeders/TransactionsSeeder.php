<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Saving;
use App\Models\Transfer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         // Ingresos de los últimos 3 meses
         $this->createIncomes();
        
         // Gastos de los últimos 3 meses
         $this->createExpenses();
         
         // Ahorros
         $this->createSavings();
         
         // Transferencias
         $this->createTransfers();
     }
 
     private function createIncomes(): void
     {
         $incomes = [
             // Mes actual
             [
                 'income_source_id' => 1, // Salario
                 'account_id' => 1, // Banco BAC
                 'amount' => 2500.00,
                 'date' => now()->startOfMonth()->addDays(14),
                 'description' => 'Salario mensual',
             ],
             [
                 'income_source_id' => 2, // Freelance
                 'account_id' => 1,
                 'amount' => 450.00,
                 'date' => now()->subDays(5),
                 'description' => 'Proyecto web para cliente',
             ],
             
             // Mes anterior
             [
                 'income_source_id' => 1,
                 'account_id' => 1,
                 'amount' => 2500.00,
                 'date' => now()->subMonth()->startOfMonth()->addDays(14),
                 'description' => 'Salario mensual',
             ],
             [
                 'income_source_id' => 3, // Inversiones
                 'account_id' => 1,
                 'amount' => 120.00,
                 'date' => now()->subMonth()->addDays(20),
                 'description' => 'Dividendos de inversiones',
             ],
             
             // Hace 2 meses
             [
                 'income_source_id' => 1,
                 'account_id' => 1,
                 'amount' => 2500.00,
                 'date' => now()->subMonths(2)->startOfMonth()->addDays(14),
                 'description' => 'Salario mensual',
             ],
             [
                 'income_source_id' => 2,
                 'account_id' => 1,
                 'amount' => 600.00,
                 'date' => now()->subMonths(2)->addDays(22),
                 'description' => 'Consultoría técnica',
             ],
         ];
 
         foreach ($incomes as $income) {
             Income::create($income);
         }
     }
 
     private function createExpenses(): void
     {
         $expenses = [
             // Mes actual
             ['expense_category_id' => 1, 'account_id' => 1, 'amount' => 85.50, 'date' => now()->subDays(2), 'description' => 'Supermercado Super Selectos', 'is_recurring' => false],
             ['expense_category_id' => 1, 'account_id' => 2, 'amount' => 35.00, 'date' => now()->subDays(3), 'description' => 'Restaurante', 'is_recurring' => false],
             ['expense_category_id' => 2, 'account_id' => 4, 'amount' => 40.00, 'date' => now()->subDays(1), 'description' => 'Gasolina', 'is_recurring' => false],
             ['expense_category_id' => 3, 'account_id' => 1, 'amount' => 600.00, 'date' => now()->startOfMonth()->addDays(5), 'description' => 'Renta mensual', 'is_recurring' => true],
             ['expense_category_id' => 4, 'account_id' => 1, 'amount' => 45.00, 'date' => now()->subDays(10), 'description' => 'Factura internet', 'is_recurring' => true],
             ['expense_category_id' => 4, 'account_id' => 1, 'amount' => 35.00, 'date' => now()->subDays(8), 'description' => 'Factura luz', 'is_recurring' => true],
             ['expense_category_id' => 5, 'account_id' => 3, 'amount' => 15.00, 'date' => now()->subDays(7), 'description' => 'Netflix', 'is_recurring' => true],
             ['expense_category_id' => 5, 'account_id' => 2, 'amount' => 25.00, 'date' => now()->subDays(4), 'description' => 'Cine', 'is_recurring' => false],
             ['expense_category_id' => 6, 'account_id' => 1, 'amount' => 50.00, 'date' => now()->subDays(12), 'description' => 'Gimnasio mensual', 'is_recurring' => true],
             ['expense_category_id' => 7, 'account_id' => 3, 'amount' => 29.00, 'date' => now()->subDays(6), 'description' => 'Libro técnico', 'is_recurring' => false],
             
             // Mes anterior
             ['expense_category_id' => 1, 'account_id' => 1, 'amount' => 120.00, 'date' => now()->subMonth()->subDays(5), 'description' => 'Supermercado', 'is_recurring' => false],
             ['expense_category_id' => 1, 'account_id' => 2, 'amount' => 45.00, 'date' => now()->subMonth()->subDays(8), 'description' => 'Restaurante', 'is_recurring' => false],
             ['expense_category_id' => 2, 'account_id' => 4, 'amount' => 50.00, 'date' => now()->subMonth()->subDays(2), 'description' => 'Gasolina', 'is_recurring' => false],
             ['expense_category_id' => 3, 'account_id' => 1, 'amount' => 600.00, 'date' => now()->subMonth()->startOfMonth()->addDays(5), 'description' => 'Renta mensual', 'is_recurring' => true],
             ['expense_category_id' => 4, 'account_id' => 1, 'amount' => 45.00, 'date' => now()->subMonth()->subDays(10), 'description' => 'Internet', 'is_recurring' => true],
             ['expense_category_id' => 5, 'account_id' => 3, 'amount' => 15.00, 'date' => now()->subMonth()->subDays(15), 'description' => 'Netflix', 'is_recurring' => true],
             ['expense_category_id' => 6, 'account_id' => 1, 'amount' => 50.00, 'date' => now()->subMonth()->subDays(12), 'description' => 'Gimnasio', 'is_recurring' => true],
             ['expense_category_id' => 8, 'account_id' => 3, 'amount' => 65.00, 'date' => now()->subMonth()->subDays(18), 'description' => 'Zapatos nuevos', 'is_recurring' => false],
             
             // Hace 2 meses
             ['expense_category_id' => 1, 'account_id' => 1, 'amount' => 110.00, 'date' => now()->subMonths(2)->subDays(3), 'description' => 'Supermercado', 'is_recurring' => false],
             ['expense_category_id' => 2, 'account_id' => 4, 'amount' => 45.00, 'date' => now()->subMonths(2)->subDays(7), 'description' => 'Gasolina', 'is_recurring' => false],
             ['expense_category_id' => 3, 'account_id' => 1, 'amount' => 600.00, 'date' => now()->subMonths(2)->startOfMonth()->addDays(5), 'description' => 'Renta mensual', 'is_recurring' => true],
             ['expense_category_id' => 9, 'account_id' => 3, 'amount' => 89.00, 'date' => now()->subMonths(2)->subDays(20), 'description' => 'Audífonos Bluetooth', 'is_recurring' => false],
         ];
 
         foreach ($expenses as $expense) {
             Expense::create($expense);
         }
     }
 
     private function createSavings(): void
     {
         $savings = [
             // Ahorros con meta
             [
                 'account_id' => 1,
                 'savings_goal_id' => 1, // Fondo de Emergencia
                 'amount' => 500.00,
                 'date' => now()->subMonth()->addDays(15),
                 'description' => 'Aporte mensual fondo emergencia',
             ],
             [
                 'account_id' => 1,
                 'savings_goal_id' => 2, // Vacaciones
                 'amount' => 300.00,
                 'date' => now()->subMonth()->addDays(15),
                 'description' => 'Ahorro para vacaciones',
             ],
             [
                 'account_id' => 1,
                 'savings_goal_id' => 3, // Laptop
                 'amount' => 200.00,
                 'date' => now()->subMonth()->addDays(15),
                 'description' => 'Ahorro para laptop',
             ],
             
             // Ahorro general (sin meta específica)
             [
                 'account_id' => 1,
                 'savings_goal_id' => null,
                 'amount' => 150.00,
                 'date' => now()->subDays(5),
                 'description' => 'Ahorro general del mes',
             ],
             [
                 'account_id' => 1,
                 'savings_goal_id' => null,
                 'amount' => 200.00,
                 'date' => now()->subMonth()->addDays(20),
                 'description' => 'Ahorro extra',
             ],
         ];
 
         foreach ($savings as $saving) {
             Saving::create($saving);
         }
     }
 
     private function createTransfers(): void
     {
         $transfers = [
             [
                 'from_account_id' => 1, // De Banco BAC
                 'to_account_id' => 2, // A Efectivo
                 'amount' => 200.00,
                 'date' => now()->subDays(10),
                 'description' => 'Retiro para gastos en efectivo',
             ],
             [
                 'from_account_id' => 1,
                 'to_account_id' => 4, // A Tarjeta débito
                 'amount' => 500.00,
                 'date' => now()->subDays(15),
                 'description' => 'Transferencia a tarjeta débito',
             ],
         ];
 
         foreach ($transfers as $transfer) {
             Transfer::create($transfer);
         }
    }
}
