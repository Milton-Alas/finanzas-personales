<?php

namespace App\Filament\Resources\Savings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers;
use App\Models\Saving;

class SavingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.name')
                    ->label('Cuenta')
                    ->searchable()
                    ->badge()
                    ->color('warning'),
                TextColumn::make('savingsGoal.name')
                    ->label('Meta')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (Saving $record): string => 
                        $record->savingsGoal ? ($record->savingsGoal->color ?? 'info') : 'gray'
                    )
                    // En v4, formatStateUsing es ideal para valores por defecto
                    ->formatStateUsing(fn (?string $state): string => $state ?? 'Ahorro General')
                    ->icon(fn (Saving $record): string => 
                        $record->savingsGoal ? ($record->savingsGoal->icon ?? 'heroicon-o-banknotes') : 'heroicon-o-sparkles'
                    ),
                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
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
                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(30)
                    ->placeholder('Sin descripción')
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
                //
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
