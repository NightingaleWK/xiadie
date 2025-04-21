<?php

namespace App\States;

use ReflectionClass;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;
use Illuminate\Support\Str;

abstract class WorkOrderState extends State
{
    abstract public function color(): string;

    abstract public function label(): string;

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

    public static function getMorphClass(): string
    {
        $shortClassName = (new ReflectionClass(static::class))->getShortName();

        return Str::snake($shortClassName);
    }
}
