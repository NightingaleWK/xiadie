<?php

declare(strict_types=1);

return [
    // 导航
    'navigation_group' => '系统设置',
    'navigation_label' => '项目管理',
    'model'            => [
        'singular' => '项目',
        'plural'   => '项目',
    ],

    // 字段
    'name'           => '项目名称',
    'name_en'        => '项目名称(英文)',
    'code'           => '项目编码',
    'description'    => '项目描述',
    'start_date'     => '立项日期',
    'operation_date' => '转运维日期',
    'end_date'       => '项目结束日期',
    'project_manager' => '项目经理',
    'manager_phone'  => '经理联系电话',
    'client_name'    => '客户单位名称',
    'client_contact' => '客户联系人',
    'client_phone'   => '客户联系电话',
    'status'         => '项目状态',
    'remarks'        => '备注',
    'created_at'     => '创建时间',
    'updated_at'     => '更新时间',

    // 区块
    'sections' => [
        'basic_info'     => '基本信息',
        'date_info'      => '日期信息',
        'contact_info'   => '联系信息',
        'client_info'    => '客户信息',
        'status_info'    => '状态信息',
    ],

    // 状态
    'statuses' => [
        'planning'    => '规划中',
        'in_progress' => '进行中',
        'operation'   => '运维中',
        'completed'   => '已完成',
        'suspended'   => '已暂停',
        'cancelled'   => '已取消',
    ],

    // 页面标题
    'pages' => [
        'index'  => '项目列表',
        'create' => '创建项目',
        'edit'   => '编辑项目',
        'view'   => '查看项目',
    ],

    // 操作
    'actions' => [
        'create' => '创建项目',
        'edit'   => '编辑',
        'delete' => '删除',
        'view'   => '查看',
    ],

    // 消息
    'messages' => [
        'created' => '项目已创建',
        'updated' => '项目已更新',
        'deleted' => '项目已删除',
    ],
];
