<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', '运维工作平台');
        $this->migrator->add('general.site_copyright', '招远中电智慧产业发展有限公司');
        $this->migrator->add('general.site_telephone', '0535-8218808');
        $this->migrator->add('general.site_address', '山东省烟台市招远市温泉路128号金融大厦10楼1002室');
    }
};
