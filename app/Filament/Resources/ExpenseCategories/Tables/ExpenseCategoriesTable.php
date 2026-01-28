<?php

namespace App\Filament\Resources\ExpenseCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

use App\Models\ExpenseCategory;


class ExpenseCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon(fn (ExpenseCategory $record): string => $record->icon ?? 'heroicon-o-tag')
                    ->iconColor(fn (ExpenseCategory $record): string => $record->color ?? 'primary'),
                TextColumn::make('budget_limit')
                    ->label('Presupuesto')
                    ->money('USD')
                    ->placeholder('Sin límite')
                    ->sortable(),
                    TextColumn::make('monthly_spent')
                    ->label('Gastado Este Mes')
                    ->money('USD')
                    ->state(fn (ExpenseCategory $record): float => $record->getMonthlySpent())
                    ->color(fn (ExpenseCategory $record): string => 
                        $record->isOverBudget() ? 'danger' : 'success'
                    )
                    ->weight('bold'),
                TextColumn::make('parent.name')
                    ->label('Categoría Padre')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Categoría principal'),
                TextColumn::make('budget_percentage')
                    ->label('% Presupuesto')
                    ->state(function (ExpenseCategory $record): string {
                        $percentage = $record->getBudgetPercentage();
                        return $percentage ? number_format($percentage, 1) . '%' : 'N/A';
                    })
                    ->badge()
                    ->color(function (ExpenseCategory $record): string { // <--- Se quitó el =>
                        if (!$record->budget_limit) return 'gray';                       
                        $percentage = $record->getBudgetPercentage();                        
                        if ($percentage >= 100) return 'danger';
                        if ($percentage >= 80) return 'warning';                        
                        return 'success';
                    })
                    ,
                IconColumn::make('is_active')
                    ->boolean(),
                    TextColumn::make('color')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('parent_id')
                    ->label('Categoría Padre')
                    ->relationship('parent', 'name')
                    ->native(false),

                TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todas')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),

                TernaryFilter::make('over_budget')
                    ->label('Presupuesto')
                    ->placeholder('Todas')
                    ->trueLabel('Sobre presupuesto')
                    ->falseLabel('Dentro del presupuesto')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('budget_limit')
                            ->whereRaw('(SELECT SUM(amount) FROM expenses WHERE expense_category_id = expense_categories.id AND YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW())) > budget_limit'),
                        false: fn ($query) => $query->whereNotNull('budget_limit')
                            ->whereRaw('(SELECT SUM(amount) FROM expenses WHERE expense_category_id = expense_categories.id AND YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW())) <= budget_limit'),
                    ),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
