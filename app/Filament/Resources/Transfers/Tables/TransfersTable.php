<?php

namespace App\Filament\Resources\Transfers\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\Summarizers;
use App\Models\Transfer;


class TransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                ->label('Fecha')
                ->date('d/m/Y')
                ->sortable()
                ->searchable(),

            // Split ayuda a organizar el Origen y Destino en la misma línea visual
            Split::make([
                TextColumn::make('fromAccount.name')
                    ->label('Origen')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('danger')
                    ->icon('heroicon-m-arrow-right'), // Heroicons v2 usa heroicon-m o -o

                TextColumn::make('toAccount.name')
                    ->label('Destino')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
            ]),

            TextColumn::make('amount')
                ->label('Monto')
                ->money('USD')
                ->sortable()
                ->weight('bold')
                ->color('info')
                ->summarize([
                    Tables\Columns\Summarizers\Sum::make()
                        ->money('USD')
                        ->label('Total'),
                ]),

            TextColumn::make('description')
                ->label('Descripción')
                ->limit(40)
                ->placeholder('Sin descripción')
                ->toggleable(),

            TextColumn::make('created_at')
                ->label('Registrado')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('from_account_id')
                ->label('Cuenta Origen')
                ->relationship('fromAccount', 'name')
                ->searchable()
                ->preload()
                ->native(false),

            SelectFilter::make('to_account_id')
                ->label('Cuenta Destino')
                ->relationship('toAccount', 'name')
                ->searchable()
                ->preload()
                ->native(false),

            Filter::make('date_range')
                ->form([
                    DatePicker::make('from')->label('Desde')->native(false),
                    DatePicker::make('until')->label('Hasta')->native(false),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('date', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('date', '<=', $date));
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['from'] ?? null) {
                        $indicators['from'] = 'Desde ' . Carbon::parse($data['from'])->format('d/m/Y');
                    }
                    if ($data['until'] ?? null) {
                        $indicators['until'] = 'Hasta ' . Carbon::parse($data['until'])->format('d/m/Y');
                    }
                    return $indicators;
                }),
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
