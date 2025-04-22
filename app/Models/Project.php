<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    /**
     * 可批量赋值的属性
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'name_en',
        'code',
        'description',
        'start_date',
        'operation_date',
        'end_date',
        'project_manager',
        'manager_phone',
        'client_name',
        'client_contact',
        'client_phone',
        'status',
        'remarks',
    ];

    /**
     * 应该被转换为日期的属性
     *
     * @var array<string>
     */
    protected $dates = [
        'start_date',
        'operation_date',
        'end_date',
        'created_at',
        'updated_at',
    ];

    /**
     * 获取项目状态列表
     *
     * @return array<string, string>
     */
    public static function getStatusOptions(): array
    {
        return [
            'planning' => '规划中',
            'in_progress' => '进行中',
            'operation' => '运维中',
            'completed' => '已完成',
            'suspended' => '已暂停',
            'cancelled' => '已取消',
        ];
    }

    /**
     * 获取项目状态名称
     *
     * @return string
     */
    public function getStatusNameAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    /**
     * 获取项目关联的工单
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
