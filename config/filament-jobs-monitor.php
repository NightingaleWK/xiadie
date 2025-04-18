<?php

return [
    'resources' => [
        'enabled' => true,
        'label' => '队列任务',
        'plural_label' => '队列任务',
        'navigation_group' => '系统设置',
        'navigation_icon' => 'heroicon-o-cpu-chip',
        'navigation_sort' => null,
        'navigation_count_badge' => false,
        'resource' => Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource::class,
        'cluster' => null,
    ],
    'pruning' => [
        'enabled' => true,
        'retention_days' => 7,
    ],
    'queues' => [
        'default',
    ],
];
