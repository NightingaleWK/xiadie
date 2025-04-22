<?php

namespace App\Models;

use App\Events\WorkOrderStatusChanged;
use App\States\Archived;
use App\States\Assigned;
use App\States\Completed;
use App\States\InProgress;
use App\States\PendingAssignment;
use App\States\PendingReview;
use App\States\Rejected;
use App\States\WorkOrderState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\ModelStates\HasStates;
use Illuminate\Support\Facades\Auth;

class WorkOrder extends Model
{
    /** @use HasFactory<\Database\Factories\WorkOrderFactory> */
    use HasFactory;
    use HasStates;

    /**
     * 可批量赋值的属性
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'creator_user_id',
        'assigned_user_id',
        'reviewer_user_id',
        'repair_details',
        'rejection_reason',
        'completed_at',
        'archived_at',
        'project_id',
        'fault_types',
    ];

    /**
     * 应该被转换为日期的属性
     *
     * @var array<string>
     */
    protected $dates = [
        'completed_at',
        'archived_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => WorkOrderState::class,
        'fault_types' => 'json',
    ];

    /**
     * 获取关联的项目
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * 获取工单创建者
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    /**
     * 获取工单处理人
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * 获取工单审核人
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_user_id');
    }

    /**
     * 获取工单历史记录
     */
    public function histories(): HasMany
    {
        return $this->hasMany(WorkOrderHistory::class);
    }

    /**
     * 指派工单给用户
     */
    public function assignTo(User $user, string $notes = '请及时维修'): void
    {
        $this->assigned_user_id = $user->id;
        $this->status->transitionTo(Assigned::class);
        $this->save();

        event(new WorkOrderStatusChanged(
            $this,
            'assign',
            'pending_assignment',
            'assigned',
            __('work-orders.messages.assigned', ['user' => $user->name]) . '。备注：' . $notes
        ));
    }

    /**
     * 开始维修
     */
    public function startRepair(): void
    {
        $this->status->transitionTo(InProgress::class);
        $this->save();

        event(new WorkOrderStatusChanged(
            $this,
            'start_repair',
            'assigned',
            'in_progress',
            __('work-orders.messages.started_repair')
        ));
    }

    /**
     * 提交审核
     */
    public function submitForReview(string $details): void
    {
        $this->repair_details = $details;
        $this->status->transitionTo(PendingReview::class);
        $this->save();

        $faultTypesLabels = [
            'power'    => __('work-orders.fault_types_options.power'),
            'network'  => __('work-orders.fault_types_options.network'),
            'device'   => __('work-orders.fault_types_options.device'),
            'config'   => __('work-orders.fault_types_options.config'),
            'software' => __('work-orders.fault_types_options.software'),
            'wiring'   => __('work-orders.fault_types_options.wiring'),
        ];

        $faultTypesText = '';
        if (!empty($this->fault_types)) {
            $types = is_array($this->fault_types) ? $this->fault_types : json_decode($this->fault_types, true);
            $typeLabels = collect($types)->map(fn($type) => $faultTypesLabels[$type] ?? $type)->join('、');
            $faultTypesText = "，故障类型：{$typeLabels}";
        }

        event(new WorkOrderStatusChanged(
            $this,
            'submit_review',
            'in_progress',
            'pending_review',
            __('work-orders.messages.submitted_review') . $faultTypesText . '。维修详情：' . $details
        ));
    }

    /**
     * 审核通过
     */
    public function approve(?string $comment = null): void
    {
        $this->completed_at = now();
        $this->status->transitionTo(Completed::class);
        $this->save();

        $message = __('work-orders.messages.approved');
        if ($comment) {
            $message .= '。审核意见：' . $comment;
        }

        event(new WorkOrderStatusChanged(
            $this,
            'approve',
            'pending_review',
            'completed',
            $message
        ));
    }

    /**
     * 审核驳回
     */
    public function reject(string $reason): void
    {
        $this->rejection_reason = $reason;
        $this->status->transitionTo(Rejected::class);
        $this->save();

        $user = Auth::user();

        event(new WorkOrderStatusChanged(
            $this,
            'reject',
            'pending_review',
            'rejected',
            __('work-orders.messages.rejected') . '。备注：' . $reason
        ));
    }

    /**
     * 重新开始维修
     */
    public function restartRepair(): void
    {
        $this->status->transitionTo(InProgress::class);
        $this->save();

        event(new WorkOrderStatusChanged(
            $this,
            'restart_repair',
            'rejected',
            'in_progress',
            __('work-orders.messages.restarted_repair')
        ));
    }

    /**
     * 拒绝指派
     */
    public function refuseAssignment(?string $reason = null): void
    {
        $this->assigned_user_id = null;
        $this->status->transitionTo(PendingAssignment::class);
        $this->save();

        $message = __('work-orders.messages.refused_assignment');
        if ($reason) {
            $message .= '。原因：' . $reason;
        }

        event(new WorkOrderStatusChanged(
            $this,
            'refuse_assignment',
            'assigned',
            'pending_assignment',
            $message
        ));
    }

    /**
     * 归档工单
     */
    public function archive(?string $comment = null): void
    {
        $this->archived_at = now();
        $this->status->transitionTo(Archived::class);
        $this->save();

        $message = __('work-orders.messages.archived');
        if ($comment) {
            $message .= '。归档意见：' . $comment;
        }

        event(new WorkOrderStatusChanged(
            $this,
            'archive',
            'completed',
            'archived',
            $message
        ));
    }
}
