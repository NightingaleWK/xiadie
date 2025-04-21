<?php

namespace App\Filament\Resources\OrganizationResource\Pages;

use App\Filament\Resources\OrganizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrganization extends ViewRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('organizations.actions.edit')),
            Actions\DeleteAction::make()
                ->label(__('organizations.actions.delete')),
        ];
    }

    public function getTitle(): string
    {
        return __('organizations.pages.view');
    }
}
