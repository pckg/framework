<?php

namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class FeatureProject extends Command
{

    public function handle()
    {
        $this->exec(
            [
                'git checkout master',
                'git pull --ff',
                'git checkout -b ' . $this->argument('branch'),
                'git push --all',
            ]
        );
    }

    protected function configure()
    {
        $this->setName('project:feature')
             ->setDescription('Create new feature branch (execute this before you start to develop new feature)')
             ->addArgument('branch', InputArgument::REQUIRED);
    }
}
