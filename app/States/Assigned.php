<?php

namespace App\States;

class Assigned extends WorkOrderState
{
    public function color(): string
    {
        return 'blue';
    }

    public function label(): string
    {
        return '已指派';
    }
}
