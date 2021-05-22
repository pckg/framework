<?php

namespace Pckg\Framework\Test;

use Pckg\Concept\Event\Dispatcher;

/**
 * Trait ListenForEvents
 * @package Pckg\Framework\Test
 */
trait ListenForEvents
{

    protected $triggeredEvents = [];

    protected function listenForEvent(string $event, $reset = true): self
    {
        if ($reset) {
            $this->triggeredEvents[$event] = 0;
        }

        $this->context->get(Dispatcher::class)->listen($event, fn() => $this->triggeredEvents[$event]++);

        return $this;
    }

    protected function getNumberOfTriggers(string $event): int
    {
        return $this->triggeredEvents[$event] ?? 0;
    }
}
