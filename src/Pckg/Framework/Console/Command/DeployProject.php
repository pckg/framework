<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CreatePckgProject
 *
 */
class DeployProject extends Command
{

    protected function configure()
    {
        $this->setName('project:deploy')
            ->setDescription('Deploy project')
            ->addOptions([
                'remote'      => 'Set remote server',
                'maintenance' => 'Put site under maintenance',
                'initial'     => 'First deploy',
                'composer'    => 'Use composer install',
                'migrations'  => 'Run migrations',
            ], InputArgument::OPTIONAL);
    }

    public function handle()
    {
    }

}
