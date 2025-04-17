<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;
use STS\FilamentImpersonate\Pages\Actions\Impersonate;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),

            Impersonate::make()
                ->record($this->getRecord())
                ->icon('heroicon-s-lock-open')
                ->color(fn() => Color::hex('#000000')),
        ];
    }

    protected function getActions(): array
    {
        return [];
    }
}
