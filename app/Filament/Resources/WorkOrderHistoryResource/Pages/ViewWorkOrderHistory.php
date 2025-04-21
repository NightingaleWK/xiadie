<?php

namespace App\Filament\Resources\WorkOrderHistoryResource\Pages;

use App\Filament\Resources\WorkOrderHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWorkOrderHistory extends ViewRecord
{
    protected static string $resource = WorkOrderHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
