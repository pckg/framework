<?php

namespace Pckg\Framework\Router\Console;

use Pckg\Framework\Console\Command;
use Pckg\Framework\Router\Command\ResolveRoute;

class MatchRoute extends Command
{
    protected function configure()
    {
        $this->setName('router:match')
             ->setDescription(
                 'Match single route'
             )
             ->addArgument('route');
    }

    public function handle()
    {
        $resolveRoute = resolve(ResolveRoute::class, ['url' => 'https://localhost' . $this->argument('route')])->execute();
        ddd($resolveRoute);
    }
}
