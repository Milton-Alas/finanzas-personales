<?php

namespace App\Filament\Resources\SavingsGoals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use App\Models\SavingsGoal;

class SavingsGoalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Meta')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon(fn (SavingsGoal $record): string => $record->icon ?? 'heroicon-o-banknotes')
                    ->iconColor(fn (SavingsGoal $record): string => $record->color ?? 'primary')
                     ,
                TextColumn::make('target_amount')
                    ->label('Objetivo')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('current_amount')
                    ->label('Actual')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                TextColumn::make('deadline')
                    ->label('Fecha Límite')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Sin fecha'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activa',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    }),
                    TextColumn::make('remaining_amount')
                    ->label('Falta')
                    ->money('USD')
                    ->state(fn (SavingsGoal $record): float => $record->getRemainingAmount())
                    ->color('warning'),

                TextColumn::make('progress')
                    ->label('Progreso')
                    ->state(function (SavingsGoal $record): string {
                        return number_format($record->getProgressPercentage(), 1) . '%';
                    })
                    ->badge()
                    ->color(function (SavingsGoal $record): string {
                        $progress = $record->getProgressPercentage();
                        if ($progress >= 100) return 'success';
                        if ($progress >= 75) return 'info';
                        if ($progress >= 50) return 'warning';
                        return 'danger';
                    }),
                TextColumn::make('color')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('icon')
                    ->searchable()
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
                //
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activa',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ])
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
