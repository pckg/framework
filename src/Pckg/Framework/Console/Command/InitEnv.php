<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class InitEnv extends Command
{

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
        $files = [
            'codeception.sample.yml'                 => 'codeception.yml',
            '.env.example' => '.env',
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
             ->addOption('app', 'app', InputArgument::OPTIONAL, 'Init app structure and configs');
    }

}
