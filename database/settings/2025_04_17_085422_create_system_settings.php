<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('default.site_name', 'Spatie');
        $this->migrator->add('default.site_active', true);
    }
};
