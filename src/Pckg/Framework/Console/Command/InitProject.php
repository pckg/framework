<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class InitProject extends Command
{

    public function symlinks()
    {
        /**
         * @T00D00 - read this from ./.pckg/pckg.yaml->service.storage
         */
        return [
            path('www') . 'cache' => path('cache') . 'www',
        ];
    }

    public function dirs()
    {
        /**
         * @T00D00 - read this from ./.pckg/pckg.yaml->service.storage
         */
        return [
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

    public function files()
    {
        return [
            // 'pckg.json' => '[]',
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

    public function createFiles()
    {
        foreach ($this->files() as $file => $content) {
            if (!is_file($file)) {
                $this->output('Creating file ' . $file);
                file_put_contents(path('root') . $file, $content);
            } else {
                $this->output('File ' . $file . ' already exists');
            }
        }
    }

    public function copyConfigs()
    {
        $env = $this->option('env', 'docker');
        $files = [
            'codeception.sample.yml' => 'codeception.yml',
            '.env.' . $env => '.env',
            '.env.example' => '.env',
            '.env.database.' . $env => '.env.database',
            '.env.redis.' . $env => '.env.redis',
            '.env.queue.' . $env => '.env.queue',
            '.env.rabbit.' . $env => '.env.rabbit',
            '.env.cache.' . $env => '.env.cache',
            '.env.web.' . $env => '.env.web',
            '.env.www.' . $env => '.env.www',
        ];

        if ($app = $this->option('app')) {
            $path = 'app' . path('ds') . $app . path('ds') . 'config' . path('ds');
            // $files[$path . 'env.sample.php'] = $path . 'env.php';
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
        $this->createFiles();
        $this->copyConfigs();
    }

    protected function configure()
    {
        $this->setName('project:init')
             ->setDescription('Initialize project (create required directories and configs)')
             ->addOption('app', 'app', InputArgument::OPTIONAL, 'Init app structure and configs')
             ->addOption('env', 'env', InputArgument::OPTIONAL, 'Default = docker');
    }

}
