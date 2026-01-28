<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers;



class AccountsBalanceWidget extends TableWidget
{
    // Orden de aparición en el Dashboard
    protected static ?int $sort = 2;
    
    // Hace que el widget ocupe todo el ancho
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(//fn (): Builder => Account::query())
            // Mostramos solo cuentas activas y ordenadas por nombre
            Account::query()->where('is_active', true)->orderBy('name')
            )
            ->columns([
                /*
                TextColumn::make('name')
                    ->label('Nombre de la Cuenta')
                    ->icon(fn ($record) => $record->icon ?? 'heroicon-o-wallet')
                    ->color(fn ($record) => $record->color ?? 'gray')
                    ->weight('bold'),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bank' => 'Banco',
                        'cash' => 'Efectivo',
                        'credit_card' => 'Tarjeta de Crédito',
                        default => $state,
                    })
                    ->color('gray'),
                TextColumn::make('balance')
                    ->label('Saldo Actual')
                    ->money('USD') 
                    ->alignment('right')
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger'),
            ])
            ->paginated(false)*/
            TextColumn::make('name')
                    ->label('Cuenta')
                    ->icon(fn (Account $record): string => $record->icon ?? 'heroicon-o-building-library')
                    ->iconColor(fn (Account $record): string => $record->color ?? 'primary')
                    ->weight('bold')
                    ->searchable(),

            TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bank' => 'info',
                        'cash' => 'success',
                        'credit_card' => 'danger',
                        'debit_card' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bank' => 'Banco',
                        'cash' => 'Efectivo',
                        'credit_card' => 'T. Crédito',
                        'debit_card' => 'T. Débito',
                        default => $state,
                    }),

            TextColumn::make('balance')
                    ->label('Balance')
                    ->money('USD')
                    ->weight('bold')
                    ->color(fn ($state): string => $state >= 0 ? 'success' : 'danger')
                    ->summarize([
                        Summarizers\Sum::make()
                            ->label('Total General')
                            ->money('USD'),
                    ]),

            TextColumn::make('income_month')
                    ->label('Ingresos (Mes)')
                    ->money('USD')
                    ->state(fn (Account $record): float => (float) $record->incomes()->currentMonth()->sum('amount'))
                    ->color('success'),

            TextColumn::make('expenses_month')
                    ->label('Gastos (Mes)')
                    ->money('USD')
                    ->state(fn (Account $record): float => (float) $record->expenses()->currentMonth()->sum('amount'))
                    ->color('danger'),
            ])
            ->heading('Balance de Cuentas')
            ->description('Vista general de todas tus cuentas activas')
            // Desactiva la paginación si tienes pocas cuentas para un look más limpio
            ->paginated(false)
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
