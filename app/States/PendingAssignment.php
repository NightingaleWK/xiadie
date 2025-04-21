<?php

namespace App\States;

class PendingAssignment extends WorkOrderState
{
    public function color(): string
    {
        return 'gray';
    }

    public function label(): string
    {
        return '待指派';
    }
}
