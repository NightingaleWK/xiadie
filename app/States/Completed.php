<?php

namespace App\States;

class Completed extends WorkOrderState
{
    public function color(): string
    {
        return 'green';
    }

    public function label(): string
    {
        return '已完成';
    }
}
