<?php

namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class PreprodProject extends Command
{
    public function handle()
    {
        $branch = $this->argument('branch');

        $this->exec(
            [
                'git checkout preprod',
                'git pull --ff',
                'git merge ' . $branch . ' --squash -m "Auto-Merge ' . $branch . ' to preprod"',
                'git push --all',
            ]
        );
    }

    protected function configure()
    {
        $this->setName('project:preprod')
             ->setDescription(
                 'Merge #branch to preprod (execute this when you want to deploy feature branch to preprod)'
             )
             ->addArgument('branch', InputArgument::REQUIRED);
    }
}
