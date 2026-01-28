<?php

namespace App\Filament\Resources\Savings;

use App\Filament\Resources\Savings\Pages\CreateSaving;
use App\Filament\Resources\Savings\Pages\EditSaving;
use App\Filament\Resources\Savings\Pages\ListSavings;
use App\Filament\Resources\Savings\Schemas\SavingForm;
use App\Filament\Resources\Savings\Tables\SavingsTable;
use App\Models\Saving;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;


class SavingResource extends Resource
{
    protected static ?string $model = Saving::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-circle';

    protected static ?string $recordTitleAttribute = 'Saving';

    protected static ?string $navigationLabel = 'Ahorros';
    
    protected static ?string $modelLabel = 'Ahorro';
    
    protected static ?string $pluralModelLabel = 'Ahorros';
    
    protected static string|UnitEnum|null $navigationGroup = 'Ahorros';
    
    protected static ?int $navigationSort = 2;


    public static function form(Schema $schema): Schema
    {
        return SavingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SavingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSavings::route('/'),
            'create' => CreateSaving::route('/create'),
            'edit' => EditSaving::route('/{record}/edit'),
        ];
    }
}
