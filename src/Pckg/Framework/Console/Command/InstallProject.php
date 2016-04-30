<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;

class InstallProject extends Command
{

    protected function configure()
    {
        $this->setName('project:install')
            ->setDescription('Install project');
    }

    public function handle()
    {
        $dirs = [
            'cache' . path('ds') . 'framework',
            'cache' . path('ds') . 'view',
            'www' . path('ds') . 'cache',
            'www' . path('ds') . 'cache' . path('ds') . 'img',
            'www' . path('ds') . 'cache' . path('ds') . 'css',
            'www' . path('ds') . 'cache' . path('ds') . 'js',
        ];

        foreach ($dirs as $dir) {
            if (!is_dir(BASE_PATH . $dir)) {
                mkdir(BASE_PATH . $dir, 0777);

            } else {
                $this->output('Directory ' . $dir . ' already exists');

            }
        }
    }

}
