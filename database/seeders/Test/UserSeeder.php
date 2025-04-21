<?php

namespace Database\Seeders\Test;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 创建工单发起人
        $creators = [
            ['name' => '张三', 'email' => 'creator1@admin.com'],
            ['name' => '李四', 'email' => 'creator2@admin.com'],
            ['name' => '王五', 'email' => 'creator3@admin.com'],
        ];

        foreach ($creators as $creator) {
            $user = User::create([
                'name' => $creator['name'],
                'email' => $creator['email'],
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('creator');
        }

        // 创建维修人员
        $repairers = [
            ['name' => '赵六', 'email' => 'repairer1@admin.com'],
            ['name' => '钱七', 'email' => 'repairer2@admin.com'],
            ['name' => '孙八', 'email' => 'repairer3@admin.com'],
            ['name' => '周九', 'email' => 'repairer4@admin.com'],
            ['name' => '吴十', 'email' => 'repairer5@admin.com'],
        ];

        foreach ($repairers as $repairer) {
            $user = User::create([
                'name' => $repairer['name'],
                'email' => $repairer['email'],
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('repairer');
        }

        // 创建审核人员
        $reviewers = [
            ['name' => '郑十一', 'email' => 'reviewer1@admin.com'],
            ['name' => '王十二', 'email' => 'reviewer2@admin.com'],
        ];

        foreach ($reviewers as $reviewer) {
            $user = User::create([
                'name' => $reviewer['name'],
                'email' => $reviewer['email'],
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('reviewer');
        }

        // 创建归档人员
        $archiver = User::create([
            'name' => '归档人员',
            'email' => 'archiver@admin.com',
            'password' => Hash::make('password'),
        ]);
        $archiver->assignRole('archiver');
    }
}
