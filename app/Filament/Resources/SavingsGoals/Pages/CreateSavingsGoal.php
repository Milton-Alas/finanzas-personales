<?php

namespace App\Filament\Resources\SavingsGoals\Pages;

use App\Filament\Resources\SavingsGoals\SavingsGoalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSavingsGoal extends CreateRecord
{
    protected static string $resource = SavingsGoalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Meta de ahorro creada exitosamente';
    }
}
