<?php

namespace App\Filament\Resources\WorkOrderResource\Pages;

use App\Filament\Resources\WorkOrderResource;
use App\Models\WorkOrder;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ListWorkOrders extends ListRecords
{
    protected static string $resource = WorkOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('全部'),

            'pending_assignment' => Tab::make(__('work-orders.statuses_no_icon.pending_assignment'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending_assignment'))
                ->badge(WorkOrder::query()->where('status', 'pending_assignment')->count())
                ->badgeColor('sky'),

            'assigned' => Tab::make(__('work-orders.statuses_no_icon.assigned'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'assigned'))
                ->badge(WorkOrder::query()->where('status', 'assigned')->count())
                ->badgeColor('purple'),

            'in_progress' => Tab::make(__('work-orders.statuses_no_icon.in_progress'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'in_progress'))
                ->badge(WorkOrder::query()->where('status', 'in_progress')->count())
                ->badgeColor('orange'),

            'pending_review' => Tab::make(__('work-orders.statuses_no_icon.pending_review'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending_review'))
                ->badge(WorkOrder::query()->where('status', 'pending_review')->count())
                ->badgeColor('indigo'),

            'rejected' => Tab::make(__('work-orders.statuses_no_icon.rejected'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'rejected'))
                ->badge(WorkOrder::query()->where('status', 'rejected')->count())
                ->badgeColor('danger'),

            'completed' => Tab::make(__('work-orders.statuses_no_icon.completed'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                ->badge(WorkOrder::query()->where('status', 'completed')->count())
                ->badgeColor('success'),

            'archived' => Tab::make(__('work-orders.statuses_no_icon.archived'))
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'archived'))
                ->badge(WorkOrder::query()->where('status', 'archived')->count())
                ->badgeColor('slate'),
        ];
    }

    public function getActiveTab(): string|null
    {
        return request()->query('tab') ?? null;
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getTableQuery();

        // 根据选中的Tab筛选
        $activeTab = $this->getActiveTab();
        if ($activeTab) {
            $query->where('status', $activeTab);
        }

        return $query;
    }
}
