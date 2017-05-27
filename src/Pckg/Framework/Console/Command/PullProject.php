<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;

class PullProject extends Command
{

    public function handle()
    {
        $this->exec(
            [
                'git pull --ff',
                'composer install --no-dev --prefer-dist',
                'bower install',
                'npm install',
            ],
            true,
            path('root')
        );
    }

    protected function configure()
    {
        $this->setName('project:pull')
             ->setDescription('Pull changes from origin and install composer (execute as many times as possible)');
    }

}
