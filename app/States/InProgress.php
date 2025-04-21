<?php

namespace App\States;

class InProgress extends WorkOrderState
{
    public function color(): string
    {
        return 'warning';
    }

    public function label(): string
    {
        return '维修中';
    }
}
