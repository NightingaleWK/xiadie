<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    public string $site_copyright;

    public string $site_telephone;

    public string $site_address;

    public static function group(): string
    {
        return 'general';
    }
}
