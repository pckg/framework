<?php

namespace Pckg\Framework\Application\Command;

use Pckg\Database\Repository\RepositoryFactory;
use Pckg\Framework\Application\Website;
use Pckg\Framework\Config;

class InitEvents
{
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function execute(callable $next)
    {
        $globalEvents = $this->config->get('pckg.dispatcher.events', []);
        collect($globalEvents)->each(fn($handlers, $event) => collect($handlers)->each(fn($handler) => dispatcher()->listen($event, $handler)));

        trigger(InitEvents::class . '.executed');

        return $next();
    }
}
