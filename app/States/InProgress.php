<?php

namespace App\States;

class InProgress extends WorkOrderState
{
    public function color(): string
    {
        return 'orange';
    }

    public function label(): string
    {
        return '维修中';
    }
}
