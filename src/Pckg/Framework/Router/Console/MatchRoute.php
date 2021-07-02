<?php

namespace Pckg\Framework\Router\Console;

use Pckg\Framework\Console\Command;
use Pckg\Framework\Router\Command\ResolveRoute;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputOption;

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
