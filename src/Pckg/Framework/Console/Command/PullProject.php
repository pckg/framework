<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class PullProject extends Command
{

    public function handle()
    {
        $execs = [
            'git pull --ff',
        ];

        if (!$this->option('no-composer')) {
            $execs[] = 'composer install --no-dev --prefer-dist';
        }

        if (!$this->option('no-bower')) {
            $execs[] = 'bower install';
        }

        if (!$this->option('no-npm')) {
            $execs[] = 'npm shrinkwrap';
        }

        $this->exec($execs, true, path('root'));
    }

    protected function configure()
    {
        $this->setName('project:pull')
             ->setDescription('Pull changes from remote and install dependencies')
             ->addOptions(
                 [
                     'no-composer' => 'No composer installs',
                     'no-bower'    => 'No bower installs',
                     'no-npm'      => 'No npm installs',
                 ],
                 InputOption::VALUE_NONE
             );
    }

}
