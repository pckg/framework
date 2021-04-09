<?php

namespace Pckg\Framework\Application\Command;

use Pckg\Database\Repository\RepositoryFactory;

class InitDatabase
{

    public function execute(callable $next)
    {
        foreach (config('database', []) as $name => $config) {
            /**
             * Skip lazy initialize connections which will be estamblished on demand.
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
