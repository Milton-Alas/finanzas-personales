<?php

namespace App\Filament\Resources\Expenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Account;
use Filament\Tables\Columns\Summarizers;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('expenseCategory.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (Expense $record): string => $record->expenseCategory->color ?? 'gray'),
                TextColumn::make('account.name')
                    ->label('Cuenta')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('danger')
                    ->summarize([
                        Summarizers\Sum::make()
                            ->money('USD')
                            ->label('Total'),
                    ]),
                TextColumn::make('date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_recurring')
                    ->label('Recurrente')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('expense_category_id')
                    ->label('Categoría')
                    ->relationship('expenseCategory', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
                SelectFilter::make('account_id')
                    ->label('Cuenta')
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
                TernaryFilter::make('is_recurring')
                    ->label('Tipo')
                    ->placeholder('Todos')
                    ->trueLabel('Solo recurrentes')
                    ->falseLabel('Solo no recurrentes'),
                SelectFilter::make('quick_filter')
                    ->label('Filtro Rápido')
                    ->options([
                        'this_month' => 'Este Mes',
                        'last_month' => 'Mes Pasado',
                        'this_year' => 'Este Año',
                        'recurring' => 'Solo Recurrentes',
                        'large' => 'Mayores a $100',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // En Filament, la data del filtro llega como un array asociativo
                        $value = $data['value'] ?? null;
                    
                        return $query->when(
                            $value,
                            fn (Builder $query, $value) => match ($value) {
                                'this_month' => $query->whereMonth('date', now()->month)
                                                     ->whereYear('date', now()->year),
                                'last_month' => $query->whereMonth('date', now()->subMonth()->month)
                                                     ->whereYear('date', now()->subMonth()->year),
                                'this_year'  => $query->whereYear('date', now()->year),
                                'recurring'  => $query->where('is_recurring', true),
                                'large'      => $query->where('amount', '>', 100),
                                default      => $query,
                            }
                        );
                    })
                    ->native(false),
            ])
            ->recordActions([
                Action::make('create_recurring')
                    ->label('Copiar')
                    ->tooltip('Crear copia para este mes')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->visible(fn (Expense $record) => (bool) $record->is_recurring)
                    ->requiresConfirmation()
                    ->modalHeading('Crear Copia de Gasto Recurrente')
                    ->modalDescription('Se creará una copia de este gasto con la fecha actual.')
                    ->modalSubmitActionLabel('Crear')
                    ->action(function (Expense $record) {
                        $newExpense = $record->replicate(['created_at', 'updated_at']);
                        $newExpense->date = now();
                        $newExpense->save();
            
                        Notification::make()
                            ->title('Gasto recurrente creado')
                            ->success()
                            ->send();
                    }),
            
                EditAction::make(),
                DeleteAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                BulkAction::make('export')
                    ->label('Exportar Seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Collection $records) {
                        return static::exportToCsv($records);
                    })
                    ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function exportToCsv($records)
    {
        $filename = 'gastos_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($records) {
            $file = fopen('php://output', 'w');
            
            // Encabezados
            fputcsv($file, ['Fecha', 'Categoría', 'Cuenta', 'Monto', 'Descripción', 'Recurrente']);

            // Datos
            foreach ($records as $expense) {
                fputcsv($file, [
                    $expense->date->format('d/m/Y'),
                    $expense->expenseCategory->name,
                    $expense->account->name,
                    number_format($expense->amount, 2),
                    $expense->description,
                    $expense->is_recurring ? 'Sí' : 'No',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
