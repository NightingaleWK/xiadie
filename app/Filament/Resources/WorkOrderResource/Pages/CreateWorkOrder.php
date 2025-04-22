<?php

namespace App\Filament\Resources\WorkOrderResource\Pages;

use App\Filament\Resources\WorkOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkOrder extends CreateRecord
{
    protected static string $resource = WorkOrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 强制设置状态为待指派
        $data['status'] = 'pending_assignment';

        // 设置当前用户为创建者
        $data['creator_user_id'] = auth()->id();

        return $data;
    }
}
