<?php

namespace App\Filament\Resources\ExpenseCategories;

use App\Filament\Resources\ExpenseCategories\Pages\CreateExpenseCategory;
use App\Filament\Resources\ExpenseCategories\Pages\EditExpenseCategory;
use App\Filament\Resources\ExpenseCategories\Pages\ListExpenseCategories;
use App\Filament\Resources\ExpenseCategories\Schemas\ExpenseCategoryForm;
use App\Filament\Resources\ExpenseCategories\Tables\ExpenseCategoriesTable;
use App\Models\ExpenseCategory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema; 
use Filament\Tables\Table;

class ExpenseCategoryResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $navigationLabel = 'Categorías de Gastos';
    
    protected static ?string $modelLabel = 'Categoría';
    
    protected static ?string $pluralModelLabel = 'Categorías de Gastos';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Configuración';
    
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        //return $schema->schema(ExpenseCategoryForm::getSchema());
        return ExpenseCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpenseCategoriesTable::configure($table);
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
            'index' => ListExpenseCategories::route('/'),
            'create' => CreateExpenseCategory::route('/create'),
            'edit' => EditExpenseCategory::route('/{record}/edit'),
        ];
    }
}
