<?php

namespace App\States;

class Archived extends WorkOrderState
{
    public function color(): string
    {
        return 'slate';
    }

    public function label(): string
    {
        return '已归档';
    }
}
