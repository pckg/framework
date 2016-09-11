<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;

class InitProject extends Command
{

    public function symlinks()
    {
        return [
            path('www') . 'cache' => path('cache') . 'www',
        ];
    }

    public function dirs()
    {
        return [
            path('storage'),
            path('storage') . 'cache',
            path('storage') . 'environment', // @T00D00 - remove this
            path('storage') . 'env',
            path('storage') . 'tmp',
            path('cache') . 'framework',
            path('cache') . 'view',
            path('cache') . 'www',
            path('cache') . 'www' . path('ds') . 'img',
            path('cache') . 'www' . path('ds') . 'css',
            path('cache') . 'www' . path('ds') . 'js',
        ];
    }

    public function handle()
    {
        foreach ($this->dirs() as $dir) {
            if (!is_dir($dir)) {
                $this->output('Creating directory ' . $dir);
                mkdir($dir, 0777, true);

            } else {
                $this->output('Directory ' . $dir . ' already exists');

            }
        }

        foreach ($this->symlinks() as $link => $target) {
            if (!is_link($link)) {
                $this->output('Creating symlink ' . $link . ' -> ' . $target);
                symlink($target, $link);

            } else {
                $this->output('Symlink/directory ' . $link . ' -> ' . $target . ' already exists');

            }
        }
    }

    protected function configure()
    {
        $this->setName('project:init')
             ->setDescription('Initialize project (create required directories)');
    }

}
