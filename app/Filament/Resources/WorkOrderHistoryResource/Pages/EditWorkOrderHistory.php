<?php

namespace App\Filament\Resources\WorkOrderHistoryResource\Pages;

use App\Filament\Resources\WorkOrderHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkOrderHistory extends EditRecord
{
    protected static string $resource = WorkOrderHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
