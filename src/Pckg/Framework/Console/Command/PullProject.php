<?php

namespace Pckg\Framework\Console\Command;

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
            $execs[] = 'composer install --no-dev --prefer-dist --ignore-platform-reqs';
        }

        if (!$this->option('no-yarn')) {
            $execs[] = 'yarn install';
        }

        if (!$this->option('no-bower')) {
            // $execs[] = 'bower install';
        }

        if (!$this->option('no-npm')) {
            // $execs[] = 'npm set progress=false && npm install --no-shrinkwrap';
        }

        if ($this->option('webpack')) {
            $execs[] = 'webpack';
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
                     'no-yarn'     => 'No yarn installs',
                     'webpack'     => 'Webpack installs',
                 ],
                 InputOption::VALUE_NONE
             );
    }
}
