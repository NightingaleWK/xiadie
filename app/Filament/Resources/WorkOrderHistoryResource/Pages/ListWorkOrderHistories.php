<?php

namespace App\Filament\Resources\WorkOrderHistoryResource\Pages;

use App\Filament\Resources\WorkOrderHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkOrderHistories extends ListRecords
{
    protected static string $resource = WorkOrderHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
