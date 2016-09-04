<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;

class UpdateProject extends Command
{

    public function handle()
    {
        $this->exec(
            [
                'git pull --ff',
                'composer update',
                'git add . --all && git commit -m "Composer update"',
            ]
        );
    }

    protected function configure()
    {
        $this->setName('project:update')
             ->setDescription('Pull changes from origin, update composer and commit changes');
    }

}
