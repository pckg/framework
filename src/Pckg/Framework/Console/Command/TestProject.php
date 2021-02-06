<?php

namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class TestProject extends Command
{

    public function handle()
    {
        $compact = $this->option('compact')
            ? ''
            : ' --steps';
        $exec = ['php vendor/bin/codecept run unit Api' . $compact];
        $packages = ['collection'];
        foreach ($packages as $package) {
            $exec[] = 'php vendor/bin/codecept run' . $compact . ' -c ./vendor/pckg/' . $package;
        }
        $this->exec($exec);
    }

    protected function configure()
    {
        $this->setName('project:test')
            ->setDescription('Test project via codeception (execute this before deploy)')
            ->addOptions(
                [
                    'compact' => 'Simplify output',
                ],
                InputOption::VALUE_NONE
            );
    }
}
