<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkOrderPolicy
{
    use HandlesAuthorization;

    /**
     * 确定用户是否可以查看工单
     */
    public function view(User $user, WorkOrder $workOrder): bool
    {
        // 管理员可以查看所有工单
        if ($user->hasRole('admin')) {
            return true;
        }

        // 用户可以查看自己创建、被指派或需要审核的工单
        return $workOrder->creator_user_id === $user->id
            || $workOrder->assigned_user_id === $user->id
            || $workOrder->reviewer_user_id === $user->id;
    }

    /**
     * 确定用户是否可以创建工单
     */
    public function create(User $user): bool
    {
        return true; // 所有登录用户都可以创建工单
    }

    /**
     * 确定用户是否可以更新工单
     */
    public function update(User $user, WorkOrder $workOrder): bool
    {
        // 管理员可以更新所有工单
        if ($user->hasRole('admin')) {
            return true;
        }

        // 创建者可以更新待指派的工单
        if ($workOrder->creator_user_id === $user->id) {
            return $workOrder->status->equals('pending_assignment');
        }

        return false;
    }

    /**
     * 确定用户是否可以删除工单
     */
    public function delete(User $user, WorkOrder $workOrder): bool
    {
        // 只有管理员可以删除工单
        return $user->hasRole('admin');
    }

    /**
     * 确定用户是否可以指派工单
     */
    public function assign(User $user, WorkOrder $workOrder): bool
    {
        // 管理员可以指派所有工单
        if ($user->hasRole('admin')) {
            return true;
        }

        // 创建者可以指派待指派的工单
        return $workOrder->creator_user_id === $user->id
            && $workOrder->status->equals('pending_assignment');
    }

    /**
     * 确定用户是否可以开始维修
     */
    public function startRepair(User $user, WorkOrder $workOrder): bool
    {
        return $workOrder->assigned_user_id === $user->id
            && $workOrder->status->equals('assigned');
    }

    /**
     * 确定用户是否可以提交审核
     */
    public function submitReview(User $user, WorkOrder $workOrder): bool
    {
        return $workOrder->assigned_user_id === $user->id
            && $workOrder->status->equals('in_progress');
    }

    /**
     * 确定用户是否可以审核工单
     */
    public function review(User $user, WorkOrder $workOrder): bool
    {
        return $workOrder->reviewer_user_id === $user->id
            && $workOrder->status->equals('pending_review');
    }

    /**
     * 确定用户是否可以重新开始维修
     */
    public function restartRepair(User $user, WorkOrder $workOrder): bool
    {
        return $workOrder->assigned_user_id === $user->id
            && $workOrder->status->equals('rejected');
    }

    /**
     * 确定用户是否可以拒绝指派
     */
    public function refuseAssignment(User $user, WorkOrder $workOrder): bool
    {
        return $workOrder->assigned_user_id === $user->id
            && $workOrder->status->equals('assigned');
    }

    /**
     * 确定用户是否可以归档工单
     */
    public function archive(User $user, WorkOrder $workOrder): bool
    {
        // 只有管理员可以归档工单
        return $user->hasRole('admin')
            && $workOrder->status->equals('completed');
    }
}
