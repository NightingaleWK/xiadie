<?php

declare(strict_types=1);

return [
    'title' => '标题',
    'description' => '描述',
    'status' => '状态',
    'creator_user_id' => '创建人',
    'assigned_user_id' => '处理人',
    'reviewer_user_id' => '审核人',
    'repair_details' => '维修记录',
    'rejection_reason' => '驳回原因',
    'completed_at' => '完成时间',
    'archived_at' => '归档时间',
    'created_at' => '创建时间',
    'updated_at' => '更新时间',

    // 状态
    'statuses' => [
        'pending_assignment' => '待指派',
        'assigned' => '已指派',
        'in_progress' => '维修中',
        'pending_review' => '待审核',
        'rejected' => '已驳回',
        'completed' => '已完成',
        'archived' => '已归档',
    ],

    // 操作
    'actions' => [
        'assign' => '指派',
        'start_repair' => '开始维修',
        'submit_review' => '提交审核',
        'approve' => '审核通过',
        'reject' => '审核驳回',
        'restart_repair' => '重新维修',
        'refuse_assignment' => '拒绝指派',
        'archive' => '归档',
    ],

    // 消息
    'messages' => [
        'assigned' => '工单已指派给 :user',
        'started_repair' => '已开始维修',
        'submitted_review' => '已提交审核',
        'approved' => '工单已审核通过',
        'rejected' => '工单已被驳回',
        'restarted_repair' => '已重新开始维修',
        'refused_assignment' => '已拒绝指派',
        'archived' => '工单已归档',
    ],
];
