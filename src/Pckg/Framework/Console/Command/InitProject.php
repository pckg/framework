<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

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
            path('storage') . 'env',
            path('storage') . 'tmp',
            path('cache') . 'framework',
            path('cache') . 'view',
            path('cache') . 'www',
            path('cache') . 'www' . path('ds') . 'img',
            path('cache') . 'www' . path('ds') . 'css',
            path('cache') . 'www' . path('ds') . 'less',
            path('cache') . 'www' . path('ds') . 'js',
        ];
    }

    public function createDirs()
    {
        foreach ($this->dirs() as $dir) {
            if (!is_dir($dir)) {
                $this->output('Creating directory ' . $dir);
                mkdir($dir, 0777, true);
            } else {
                $this->output('Directory ' . $dir . ' already exists');
            }
        }
    }

    public function createSymlinks()
    {
        foreach ($this->symlinks() as $link => $target) {
            if (!is_link($link)) {
                $this->output('Creating symlink ' . $link . ' -> ' . $target);
                symlink($target, $link);
            } else {
                $this->output('Symlink/directory ' . $link . ' -> ' . $target . ' already exists');
            }
        }
    }

    public function copyConfigs()
    {
        $files = [
            'codeception.sample.yml'                 => 'codeception.yml',
            'config' . path('ds') . 'env.sample.php' => 'config' . path('ds') . 'env.php',
        ];

        if ($app = $this->option('app')) {
            $path = 'app' . path('ds') . $app . path('ds') . 'config' . path('ds');
            $files[$path . 'env.sample.php'] = $path . 'env.php';
        }

        foreach ($files as $original => $copy) {
            if (is_file(path('root') . $original)) {
                if (!is_file(path('root') . $copy)) {
                    $this->output('Copying ' . $original . ' => ' . $copy);
                    file_put_contents(path('root') . $copy, file_get_contents(path('root') . $original));
                    $this->output('Copied!');
                } else {
                    $this->output('File ' . $copy . ' already exists.');
                }
            } else {
                $this->output('Original file ' . $original . ' does not exist.');
            }
        }
    }

    public function handle()
    {
        $this->createDirs();
        $this->createSymlinks();
        $this->copyConfigs();
    }

    protected function configure()
    {
        $this->setName('project:init')
             ->setDescription('Initialize project (create required directories and configs)')
             ->addOption('app', 'app', InputArgument::OPTIONAL, 'Init app structure and configs');
    }

}
