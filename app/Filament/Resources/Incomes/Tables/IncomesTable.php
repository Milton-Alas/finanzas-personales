<?php

namespace App\Filament\Resources\Incomes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filaments\Resources\Incomes\Models\Income;
use Filaments\Resources\Incomes\Models\IncomeSource;
use Filament\Tables\Columns\Summarizers;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;


class IncomesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('incomeSource.name')
                    ->label('Fuente')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
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
                SelectFilter::make('income_source_id')
                    ->label('Fuente de Ingreso')
                    ->relationship('incomeSource', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
                SelectFilter::make('account_id')
                    ->label('Cuenta')
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

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
