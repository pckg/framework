<?php

namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CreatePckgProject
 *
 */
class ClearCache extends Command
{
    public function handle()
    {
        $path = path('cache') . 'framework' . path('ds');
        if (!$this->option('skip-database')) {
            $this->unlink($path, 'database');
        }

        if (!$this->option('skip-defaults')) {
            $this->unlink($path, 'defaults');
        }

        if (!$this->option('skip-router')) {
            $this->unlink($path, 'router');
        }
    }

    public function unlink($path, $regex)
    {
        foreach (scandir($path) as $file) {
            if (strpos($file, $regex)) {
                unlink($path . $file);
            }
        }
    }

    protected function configure()
    {
        $this->setName('cache:clear')
             ->setDescription('Clear cache')
             ->addOptions([
                     'skip-database' => 'Skip database cache clear',
                     'skip-defaults' => 'Skip defaults cache clear',
                     'skip-router'   => 'Skip router cache clear',
                     'skip-view'     => 'Skip router cache clear',
                     'skip-css'      => 'Skip CSS cache clear',
                     'skip-js'       => 'Skip JS cache clear',
                     'skip-img'      => 'Skip IMG cache clear',
                 ], InputArgument::OPTIONAL);
    }
}
