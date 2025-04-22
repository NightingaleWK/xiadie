<?php

namespace Database\Seeders\Test;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 查找招远中电智慧产业发展有限公司及其子组织
        $zhaoyuan = Organization::where('name', '招远中电智慧产业发展有限公司')->first();

        if (!$zhaoyuan) {
            $this->command->error('招远中电智慧产业发展有限公司组织不存在，请先运行 OrganizationSeeder');
            return;
        }

        // 获取招远中电智慧产业发展有限公司的所有子组织
        $departments = $zhaoyuan->children()->get();
        $departmentIds = $departments->pluck('id')->toArray();

        // 如果没有子部门，则直接使用招远中电的ID
        if (empty($departmentIds)) {
            $departmentIds = [$zhaoyuan->id];
        }

        // 创建工单发起人
        $creators = [
            ['name' => '张三-发起人', 'email' => 'creator1@admin.com'],
        ];

        foreach ($creators as $creator) {
            $user = User::create([
                'name' => $creator['name'],
                'email' => $creator['email'],
                'password' => Hash::make('password'),
                'organization_id' => $this->getRandomDepartment($departmentIds),
            ]);
            $user->assignRole('creator');
        }

        // 创建维修人员
        $repairers = [
            ['name' => '赵六-维修人员', 'email' => 'repairer1@admin.com'],
            ['name' => '钱七-维修人员', 'email' => 'repairer2@admin.com'],
            ['name' => '孙八-维修人员', 'email' => 'repairer3@admin.com'],
        ];

        foreach ($repairers as $repairer) {
            $user = User::create([
                'name' => $repairer['name'],
                'email' => $repairer['email'],
                'password' => Hash::make('password'),
                'organization_id' => $this->getRandomDepartment($departmentIds),
            ]);
            $user->assignRole('repairer');
        }

        // 创建审核人员
        $reviewers = [
            ['name' => '郑十一-审核人员', 'email' => 'reviewer1@admin.com'],
        ];

        foreach ($reviewers as $reviewer) {
            $user = User::create([
                'name' => $reviewer['name'],
                'email' => $reviewer['email'],
                'password' => Hash::make('password'),
                'organization_id' => $this->getRandomDepartment($departmentIds),
            ]);
            $user->assignRole('reviewer');
        }

        // 创建归档人员
        $archiver = User::create([
            'name' => '张三十三-归档人员',
            'email' => 'archiver@admin.com',
            'password' => Hash::make('password'),
            'organization_id' => $zhaoyuan->id, // 归档人员直接属于招远中电
        ]);
        $archiver->assignRole('archiver');
    }

    /**
     * 随机获取一个部门ID
     * 
     * @param array $departmentIds 部门ID数组
     * @return int 随机选择的部门ID
     */
    private function getRandomDepartment(array $departmentIds): int
    {
        return $departmentIds[array_rand($departmentIds)];
    }
}
