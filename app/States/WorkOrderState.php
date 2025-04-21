<?php

namespace App\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class WorkOrderState extends State
{
    abstract public function color(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(PendingAssignment::class)
            ->allowTransition(PendingAssignment::class, Assigned::class)
            ->allowTransition(Assigned::class, InProgress::class)
            ->allowTransition(Assigned::class, PendingAssignment::class)
            ->allowTransition(InProgress::class, PendingReview::class)
            ->allowTransition(PendingReview::class, Completed::class)
            ->allowTransition(PendingReview::class, Rejected::class)
            ->allowTransition(Rejected::class, InProgress::class)
            ->allowTransition(Completed::class, Archived::class);
    }
}
