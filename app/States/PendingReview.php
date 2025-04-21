<?php

namespace App\States;

class PendingReview extends WorkOrderState
{
    public function color(): string
    {
        return 'dark';
    }

    public function label(): string
    {
        return '待审核';
    }
}
