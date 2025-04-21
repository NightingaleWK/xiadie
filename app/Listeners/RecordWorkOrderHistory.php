<?php

namespace App\Listeners;

use App\Events\WorkOrderStatusChanged;
use App\Models\WorkOrderHistory;
use Illuminate\Support\Facades\Auth;

class RecordWorkOrderHistory
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(WorkOrderStatusChanged $event): void
    {
        WorkOrderHistory::create([
            'work_order_id' => $event->workOrder->id,
            'user_id' => Auth::id(),
            'action' => $event->action,
            'from_status' => $event->fromStatus,
            'to_status' => $event->toStatus,
            'comment' => $event->comment,
        ]);
    }
}
