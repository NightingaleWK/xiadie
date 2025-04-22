<?php

declare(strict_types=1);

return [
    // 通用
    'title' => '工单历史',

    'work_order_id' => '工单ID',
    'user_id'       => '操作人',
    'action'        => '操作',
    'from_status'   => '原状态',
    'to_status'     => '新状态',
    'comment'       => '备注',
    'created_at'    => '操作时间',

    // 操作类型
    'actions' => [
        'create'            => '创建工单',
        'assign'            => '指派工单',
        'start_repair'      => '开始维修',
        'submit_review'     => '提交审核',
        'approve'           => '审核通过',
        'reject'            => '审核驳回',
        'restart_repair'    => '重新维修',
        'refuse_assignment' => '拒绝指派',
        'archive'           => '归档工单',
    ],

    // 消息
    'messages' => [
        'created'            => '创建了工单',
        'assigned'           => '将工单指派给 :user',
        'started_repair'     => '开始维修工单',
        'submitted_review'   => '提交工单审核',
        'approved'           => '审核通过了工单',
        'rejected'           => '驳回了工单',
        'restarted_repair'   => '重新开始维修工单',
        'refused_assignment' => '拒绝了工单指派',
        'archived'           => '归档了工单',
    ],
];
