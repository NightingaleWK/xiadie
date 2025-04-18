<?php

namespace Database\Seeders;

use Database\Seeders\Test\UserSeeder;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
    }
}
