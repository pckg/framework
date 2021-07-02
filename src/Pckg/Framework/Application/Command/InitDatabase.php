<?php

namespace Pckg\Framework\Application\Command;

use Pckg\Database\Repository\RepositoryFactory;
use Pckg\Framework\Config;

class InitDatabase
{

    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function execute(callable $next)
    {
        foreach ($this->config->get('database', []) as $name => $config) {
            /**
             * Skip lazy initialize connections which will be established on demand.
             */
            if (is_string($config) || ($config['lazy'] ?? false)) {
                continue;
            }

            measure('Connecting to database ' . $name, function () use ($config, $name) {
                RepositoryFactory::createRepositoryConnection($config, $name);
            });
        }

        return $next();
    }
}
