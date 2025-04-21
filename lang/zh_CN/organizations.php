<?php

declare(strict_types=1);

return [
    // 导航
    'navigation_group' => '组织结构',
    'navigation_label' => '组织管理',
    'model' => [
        'singular' => '组织',
        'plural' => '组织',
    ],

    // 字段
    'name' => '名称',
    'code' => '编码',
    'description' => '描述',
    'parent_id' => '上级组织',
    'hierarchy_path' => '组织层级',
    'level' => '层级',
    'path' => '路径',
    'is_active' => '激活状态',
    'created_at' => '创建时间',
    'updated_at' => '更新时间',

    // 区块
    'sections' => [
        'basic_info' => '基本信息',
        'hierarchy_info' => '层级信息',
        'status_info' => '状态信息',
    ],

    // 状态
    'statuses' => [
        'active' => '激活',
        'inactive' => '未激活',
    ],

    // 页面标题
    'pages' => [
        'index' => '组织列表',
        'create' => '创建组织',
        'edit' => '编辑组织',
        'view' => '查看组织',
    ],

    // 操作
    'actions' => [
        'create' => '创建组织',
        'edit' => '编辑',
        'delete' => '删除',
        'activate' => '激活',
        'deactivate' => '禁用',
    ],

    // 消息
    'messages' => [
        'created' => '组织已创建',
        'updated' => '组织已更新',
        'deleted' => '组织已删除',
        'activated' => '组织已激活',
        'deactivated' => '组织已禁用',
    ],
];
