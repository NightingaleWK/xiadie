<?php

namespace App\States;

class PendingAssignment extends WorkOrderState
{
    public function color(): string
    {
        return 'info';
    }

    public function label(): string
    {
        return '待指派';
    }
}
