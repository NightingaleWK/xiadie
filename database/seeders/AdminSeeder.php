<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => '管理员',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Admin@123'),
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
    }
}
