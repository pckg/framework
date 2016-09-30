<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;

class TestProject extends Command
{

    public function handle()
    {
        $exec = ['codecept run --steps'];
        $packages = ['database'];
        foreach ($packages as $package) {
            $exec[] = 'codecept run --steps -c ./vendor/pckg/' . $package;
        }
        $this->exec($exec);
    }

    protected function configure()
    {
        $this->setName('project:test')
             ->setDescription('Test project via codeception (execute this before deploy)');
    }

}
