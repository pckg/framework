<?php

namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ProdProject extends Command
{
    public function handle()
    {
        $branch = $this->argument('branch');

        $this->exec(
            [
                'git checkout master',
                'git pull --ff',
                'git merge ' . $branch . ' --squash -m "Auto-Merge ' . $branch . ' to master"',
                'git push --all',
            ]
        );
    }

    protected function configure()
    {
        $this->setName('project:master')
             ->setDescription('Merge #branch to prod (be careful!)')
             ->addArgument('branch', InputArgument::REQUIRED);
    }
}
