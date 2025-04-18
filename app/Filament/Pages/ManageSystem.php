<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageSystem extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = '系统设置';
    protected static ?string $title = '常规设置';
    protected static string $settings = GeneralSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('site_name')
                    ->label(__('system.site_name'))
                    ->required(),

                TextInput::make('site_telephone')
                    ->label(__('system.site_telephone'))
                    ->required(),

                TextInput::make('site_address')
                    ->label(__('system.site_address'))
                    ->required(),

                TextInput::make('site_copyright')
                    ->label(__('system.site_copyright'))
                    ->required(),
            ]);
    }
}
