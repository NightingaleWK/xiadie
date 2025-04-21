<?php

namespace App\States;

class Rejected extends WorkOrderState
{
    public function color(): string
    {
        return 'danger';
    }

    public function label(): string
    {
        return '已驳回';
    }
}
