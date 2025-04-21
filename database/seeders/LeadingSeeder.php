<?php

namespace Database\Seeders;

use Database\Seeders\Leading\AdminSeeder;
use Database\Seeders\Leading\OrganizationSeeder;
use Database\Seeders\Leading\ProjectSeeder;
use Database\Seeders\Leading\RolePermissionSeeder;
use Illuminate\Database\Seeder;

class LeadingSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminSeeder::class,
            ProjectSeeder::class,
            OrganizationSeeder::class,
        ]);
    }
}
