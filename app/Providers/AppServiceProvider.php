<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 注册额外的颜色
        \Filament\Support\Facades\FilamentColor::register([
            // 保留原有默认颜色
            'danger' => \Filament\Support\Colors\Color::Red,
            'gray' => \Filament\Support\Colors\Color::Zinc,
            'info' => \Filament\Support\Colors\Color::Blue,
            'primary' => \Filament\Support\Colors\Color::Amber,
            'success' => \Filament\Support\Colors\Color::Green,
            'warning' => \Filament\Support\Colors\Color::Yellow,

            // 添加额外颜色
            'purple' => \Filament\Support\Colors\Color::Purple,
            'indigo' => \Filament\Support\Colors\Color::Indigo,
            'sky' => \Filament\Support\Colors\Color::Sky,
            'teal' => \Filament\Support\Colors\Color::Teal,
            'emerald' => \Filament\Support\Colors\Color::Emerald,
            'lime' => \Filament\Support\Colors\Color::Lime,
            'orange' => \Filament\Support\Colors\Color::Orange,
            'pink' => \Filament\Support\Colors\Color::Pink,
            'cyan' => \Filament\Support\Colors\Color::Cyan,
            'slate' => \Filament\Support\Colors\Color::Slate,
            'stone' => \Filament\Support\Colors\Color::Stone,
        ]);
    }
}
