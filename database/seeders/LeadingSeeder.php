<?php

namespace Database\Seeders;

use Database\Seeders\Leading\AdminSeeder;
use Illuminate\Database\Seeder;

class LeadingSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
        ]);
    }
}
