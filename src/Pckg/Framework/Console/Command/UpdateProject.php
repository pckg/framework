<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;

class UpdateProject extends Command
{

    public function handle()
    {
        $this->exec(
            [
                'Pulling git changes' => 'git pull --ff',
                'Updating composer'   => 'composer update',
                'Updating yarn'       => 'yarn upgrade',
                //'Updating bower'      => 'bower update',
                //'Updating npm'      => 'npm shrinkwrap',
                'Commiting changes'   => 'git add . --all && git commit -m "Dependency update"',
            ]
        );
    }

    protected function configure()
    {
        $this->setName('project:update')
             ->setDescription(
                 'Pull changes from origin, update composer and commit changes (execute this when you need to update dependencies)'
             );
    }

}
