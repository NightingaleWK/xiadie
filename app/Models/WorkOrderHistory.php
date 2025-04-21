<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderHistory extends Model
{
    use HasFactory;

    /**
     * 可批量赋值的属性
     *
     * @var array<string>
     */
    protected $fillable = [
        'work_order_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'comment',
    ];

    /**
     * 获取关联的工单
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * 获取操作人
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
