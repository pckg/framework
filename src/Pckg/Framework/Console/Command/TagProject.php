<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class TagProject extends Command
{

    public function handle()
    {
        $packets = [
            'auth',
            'collection',
            'concept',
            'database',
            'framework',
            'generic',
            'htmlbuilder',
            'import',
            'mail',
            'manager',
            'migrator',
            'payment',
            'queue',
            'tempus',
            'translator',
            'charts',
        ];

        $tag = $this->option('tag');

        foreach ($packets as $packet) {
            $dir = path('root') . 'vendor/pckg/' . $packet;
            $this->exec(['cd ' . $dir . ' && git tag ' . $tag . ' && git push --tags']);
        }
    }

    protected function configure()
    {
        $this->setName('project:tag')
             ->setDescription('Tag pckg project dependencies')
             ->addOptions([
                              'tag' => 'Enter tag name',
                          ], InputOption::VALUE_REQUIRED);
    }

}
