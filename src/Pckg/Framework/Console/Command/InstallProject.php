<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;

class InstallProject extends Command
{

    public function symlinks()
    {
        return [
            path('cache') . 'www' => 'www' . path('ds') . 'cache',
        ];
    }

    public function dirs()
    {
        return [
            path('storage'),
            path('cache') . 'framework',
            path('cache') . 'view',
            path('cache') . 'www' . path('ds') . 'img',
            path('cache') . 'www' . path('ds') . 'css',
            path('cache') . 'www' . path('ds') . 'js',
            path('storage') . 'tmp',
            path('storage') . 'environment',
        ];
    }

    public function handle()
    {
        foreach ($this->dirs() as $dir) {
            if (!is_dir(BASE_PATH . $dir)) {
                $this->output('Creating directory ' . $dir);
                mkdir(BASE_PATH . $dir, 0777, true);

            } else {
                $this->output('Directory ' . $dir . ' already exists');

            }
        }

        foreach ($this->symlinks() as $target => $link) {
            if (!is_link(BASE_PATH . $link)) {
                $this->output('Creating symlink ' . $target . ' -> ' . $link);
                symlink($target, $link);

            } else {
                $this->output('Symlink ' . $target . ' -> ' . $link . ' already exists');

            }
        }
    }

    protected function configure()
    {
        $this->setName('project:install')
             ->setDescription('Install project');
    }

}
