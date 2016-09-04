<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;

class TestProject extends Command
{

    public function handle()
    {
        $this->exec(
            [
                'php vendor/bin/codecept run --steps',
            ]
        );
    }

    protected function configure()
    {
        $this->setName('project:test')
             ->setDescription('Test project via codeception (execute this before deploy)');
    }

}
