<?php

namespace App\States;

class Assigned extends WorkOrderState
{
    public function color(): string
    {
        return 'purple';
    }

    public function label(): string
    {
        return '已指派';
    }
}
