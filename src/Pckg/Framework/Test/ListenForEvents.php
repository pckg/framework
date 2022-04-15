<?php

namespace Pckg\Framework\Test;

use Pckg\Concept\Event\Dispatcher;

/**
 * Trait ListenForEvents
 * @package Pckg\Framework\Test
 */
trait ListenForEvents
{
    protected array $triggeredEvents = [];

    protected function listenForEvents(array $events, $reset = true): self
    {
        foreach ($events as $event) {
            $this->listenForEvent($event, $reset);
        }

        return $this;
    }

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

    protected function hasTriggered($events, int $min = 1): bool
    {
        if (!is_array($events)) {
            $events = [$events];
        }

        foreach ($events as $event) {
            if ($this->getNumberOfTriggers($event) < $min) {
                return false;
            }
        }

        return true;
    }
}
