<?php

namespace App\Filament\Resources\SavingsGoals;

use App\Filament\Resources\SavingsGoals\Pages\CreateSavingsGoal;
use App\Filament\Resources\SavingsGoals\Pages\EditSavingsGoal;
use App\Filament\Resources\SavingsGoals\Pages\ListSavingsGoals;
use App\Filament\Resources\SavingsGoals\Schemas\SavingsGoalForm;
use App\Filament\Resources\SavingsGoals\Tables\SavingsGoalsTable;
use App\Models\SavingsGoal;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Schemas\Schema;
use Filament\Tables\Table;


class SavingsGoalResource extends Resource
{
    protected static ?string $model = SavingsGoal::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Metas de Ahorro';

    protected static ?int $navigationSort = 3;

    //protected static ?string $slug = 'SavingsGoal';

    protected static ?string $modelLabel = 'Metas de Ahorro';
    
    protected static ?string $pluralModelLabel = 'Metas de Ahorro';

    protected static string|\UnitEnum|null $navigationGroup = 'Ahorros';

    public static function form(Schema $schema): Schema
    {
       // return $schema
         //   ->schema(SavingsGoalForm::getSchema());
            return SavingsGoalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SavingsGoalsTable::configure($table);
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
            'index' => ListSavingsGoals::route('/'),
            'create' => CreateSavingsGoal::route('/create'),
            'edit' => EditSavingsGoal::route('/{record}/edit'),
        ];
    }
}
