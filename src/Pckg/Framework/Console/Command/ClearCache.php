<?php namespace Pckg\Framework\Console\Command;

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
        $path = BASE_PATH . 'cache' . path('ds') . 'framework' . path('ds');

        if ($this->option('skip-database')) {
            $this->output('Skipping database cache');
        } else {
            $this->output('Clearing database cache');
            $this->unlink($path, 'database');
            $this->output('Database cache cleared');
        }

        if ($this->option('skip-defaults')) {
            $this->output('Skipping defaults cache');
        } else {
            $this->output('Clearing defaults cache');
            $this->unlink($path, 'defaults');
            $this->output('Defaults cache cleared');
        }

        if ($this->option('skip-router')) {
            $this->output('Skipping router cache');
        } else {
            $this->output('Clearing router cache');
            $this->unlink($path, 'router');
            $this->output('Router cache cleared');
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
             ->addOptions(
                 [
                     'skip-database' => 'Skip database cache clear',
                     'skip-defaults' => 'Skip defaults cache clear',
                     'skip-router'   => 'Skip router cache clear',
                     'skip-view'     => 'Skip router cache clear',
                     'skip-css'      => 'Skip CSS cache clear',
                     'skip-js'       => 'Skip JS cache clear',
                     'skip-img'      => 'Skip IMG cache clear',
                 ],
                 InputArgument::OPTIONAL
             );
    }

}
