<?php

namespace Database\Seeders;

use Database\Seeders\Trailing\SuperAdminRoleSeeder;
use Illuminate\Database\Seeder;

class TrailingSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperAdminRoleSeeder::class,
        ]);
    }
}
