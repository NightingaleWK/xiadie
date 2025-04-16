<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 调用AdminSeeder创建管理员账户
        $this->call([
            AdminSeeder::class,
        ]);

        // 创建10个随机用户
        User::factory(10)->create();
    }
}
