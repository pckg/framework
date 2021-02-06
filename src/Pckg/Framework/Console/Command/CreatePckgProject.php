<?php

namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class CreatePckgProject
 *
 */
class CreatePckgProject extends Command
{
    use AddDatabaseConnection;

/**
     * @var string
     */


    protected $app;
    protected function configure()
    {
        $this->setName('app:create')
             ->setDescription('Create new application')
             ->addArguments([
                     'app'      => 'App name',
                     'hosts'    => 'List of hostnames, separated by ,',
                     'database' => 'Default database config',
                     'composer' => 'List of composer dependencies, separated by ,',
                 ], InputArgument::OPTIONAL)
             ->addOptions([
                     'skip-existance-check' => 'Disable check if directories already exists',
                     'skip-dir-creation'    => 'Disable app directory creation',
                     'skip-app-creation'    => 'Disable creation of app class',
                     'skip-router'          => 'Disable global router changes',
                     'skip-database'        => 'Disable database configuration',
                     'skip-composer'        => 'Disable composer requirements',
                     'skip-commit'          => 'Disable commit after creation',
                 ], InputOption::VALUE_OPTIONAL | InputOption::VALUE_NONE);
    }

    public function handle()
    {
        $this->fetchAppName();
        $this->checkExistance();
        $this->createDirectories();
        $this->createPhpApp();
        $this->addToRouter();
        $this->addDatabaseConnection();
        $this->addComposerDependencies();
        $this->commitChanges();
        $this->finishCreation();
    }

    protected function fetchAppName()
    {
        $this->app = $this->argument('app');
        if (!$this->app) {
            $this->app = $this->askQuestion('Application name: ');
        }

        if (!$this->app) {
            $this->quit("App name is required, quitting ...");
        }
    }

    protected function checkExistance()
    {
        if ($this->option('skip-existance-check')) {
            return $this->output('Skipping existance check');
        }

        $path = path('apps') . $this->app;
        if (is_dir($path)) {
            $this->quit('Path ' . $path . ' already exists');
        }
    }

    protected function createDirectories()
    {
        if ($this->option('skip-dir-creation')) {
            return $this->output('Skipping directory creation.');
        }

        $this->output('Creating directories.');
/**
         * Create paths.
         */
        $path = path('apps') . $this->app . path('ds');
        mkdir($path, 0777, true);
        mkdir($path . 'config', 0777);
        mkdir($path . 'src', 0777);
        mkdir($path . 'public', 0777);
        $this->output('Directories created.');
    }

    /**
     * Ask for parent (Website, Api, Console).
     * Create .php file in app src directory.
     */
    protected function createPhpApp()
    {
        if ($this->option('skip-app-creation')) {
            return $this->output('Skipping app class creation');
        }

        $this->output('Creating app file.');
/**
         * Build path and content.
         */
        $path = path('apps') . $this->app . path('ds') . 'src' . path('ds') . ucfirst($this->app) . '.php';
        $content = '<?php

use Pckg\Framework\Provider;

class ' . ucfirst($this->app) . ' extends Provider
{

}
';
/**
         * Create file.
         */
        file_put_contents($path, $content);
        $this->output('App file created.');
    }

    /**
     * Ask user if he wants to add it to router.
     * Ask for domains.
     * Alter router.
     */
    protected function addToRouter()
    {
        if ($this->option('skip-router')) {
            return $this->output('Skipping global router changes');
        }

        if ($hosts = $this->argument('hosts')) {
            $hosts = explode(',', $hosts);
        } else {
            $hosts = [];
            do {
                $host = $this->askQuestion('Enter hostname:');
            } while (($host && $hosts[] = $host) || $host);
        }

        $this->output('Updating global router');
/**
         * First we build array.
         */
        $append = '    \'' . $this->app . '\' => [
            \'host\' => [';
        foreach ($hosts as $host) {
            $append .= '
                \'' . $host . '\',';
        }
        if ($hosts) {
            $append .= '
            ';
        }
        $append .= '],
        ],
    ';
/**
         * Read current router content.
         */
        $routerPath = BASE_PATH . 'config' . path('ds') . 'router.php';
        $router = file_get_contents($routerPath);
/**
         * Build new router.
         */
        $newRouter = str_lreplace('],', $append . '],', $router);
/**
         * Write new router.
         */
        file_put_contents($routerPath, $newRouter);
        $this->output('Global router has been updated.');
    }

    /**
     * Ask user for composer dependencies.
     * Require them.
     */
    protected function addComposerDependencies()
    {
        if ($this->option('skip-composer')) {
            return $this->output('Skipping composer dependencies');
        }

        if ($dependencies = $this->argument('composer')) {
            $dependencies = explode(',', $dependencies);
        } else {
            $dependencies = [];
            do {
                $dependency = $this->askQuestion('Enter composer dependency:');
            } while (($dependency && $dependencies[] = $dependency) || $dependency);
        }

        $this->output('Requiring dependencies.');
/**
         * Simply require each dependency separately.
         */
        $this->exec(array_map(function ($dependency) {

                    return 'composer require ' . $dependency;
        }, $dependencies));
        $this->output('Dependencies required.');
    }

    /**
     * Ask user if he wants to commit changes.
     * Add created and changed files.
     */
    protected function commitChanges()
    {
        if ($this->option('skip-commit') || !$this->askConfirmation('Do you want to commit changes?')) {
            return $this->output('Skipping commit.');
        }

        $this->output('Committing changes.');
/**
         * We'll simply execute few git commands.
         */
        $this->exec([
                'git add app/' . $this->app . ' --all',
                'git add config/router.php',
                'git add composer.json',
                'git add composer.lock',
                'git commit -m "Created app \'' . $this->app . '\'"',
                'git push --all',
            ]);
        $this->output('Changes commited.');
    }

    protected function finishCreation()
    {
        return $this->output("App " . $this->app . ' created');
    }
}
