<?php

namespace Database\Seeders\Leading;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 重置缓存
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 创建权限
        $permissions = [
            // 工单相关权限
            'view_any_work::order',
            'view_work::order',
            'create_work::order',
            'update_work::order',
            'delete_work::order',
            'delete_any_work::order',
            'force_delete_work::order',
            'force_delete_any_work::order',
            'restore_work::order',
            'restore_any_work::order',
            'replicate_work::order',
            'reorder_work::order',

            // 工单操作权限
            'assign_work::order',      // 指派工单
            'start_repair_work::order', // 开始维修
            'submit_review_work::order', // 提交审核
            'approve_work::order',     // 审核通过
            'reject_work::order',      // 审核驳回
            'restart_repair_work::order', // 重新维修
            'refuse_assignment_work::order', // 拒绝指派
            'reassign_work::order',    // 重新指派
            'archive_work::order',     // 归档工单

            // 用户管理权限
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',
            'force_delete_user',
            'force_delete_any_user',
            'restore_user',
            'restore_any_user',
            'replicate_user',
            'reorder_user',

            // 角色管理权限
            'view_any_role',
            'view_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        $creator = Role::create([
            'name' => 'creator',
            'nick_name' => '工单创建者',
            'guard_name' => 'web'
        ]);
        $creator->givePermissionTo([
            'view_any_work::order',
            'view_work::order',
            'create_work::order',
            'update_work::order',
        ]);

        $repairer = Role::create([
            'name' => 'repairer',
            'nick_name' => '维修人员',
            'guard_name' => 'web'
        ]);
        $repairer->givePermissionTo([
            'view_any_work::order',
            'view_work::order',
            'start_repair_work::order',
            'submit_review_work::order',
            'restart_repair_work::order',
            'refuse_assignment_work::order',
        ]);

        $reviewer = Role::create([
            'name' => 'reviewer',
            'nick_name' => '审核人员',
            'guard_name' => 'web'
        ]);
        $reviewer->givePermissionTo([
            'view_any_work::order',
            'view_work::order',
            'approve_work::order',
            'reject_work::order',
        ]);

        $archiver = Role::create([
            'name' => 'archiver',
            'nick_name' => '归档人员',
            'guard_name' => 'web'
        ]);
        $archiver->givePermissionTo([
            'view_any_work::order',
            'view_work::order',
            'archive_work::order',
        ]);
    }
}
