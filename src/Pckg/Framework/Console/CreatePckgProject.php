<?php namespace Pckg\Framework\Console;

use Pckg\Framework\Console\Command\AddDatabaseConnection;
use Symfony\Component\Console\Input\InputArgument;

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
            ->addArgument('app', InputArgument::OPTIONAL);
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
        $this->app = $this->input->getArgument('app');
        if (!$this->app) {
            $this->app = $this->askQuestion('Name of app: ');
        }

        if (!$this->app) {
            $this->quit("Empty answer");
        }
    }

    protected function checkExistance()
    {
        $path = path('apps') . $this->app;

        if (is_dir($path)) {
            $this->quit('Path ' . $path . ' already exists');
        }
    }

    protected function createDirectories()
    {
        $this->output('Creating directories.');

        /**
         * Create paths.
         */
        $path = path('apps') . $this->app . path('ds');
        mkdir($path, 0777);
        mkdir($path . 'config', 0777);
        mkdir($path . 'src', 0777);

        $this->output('Directories created.');
    }

    /**
     * Ask for parent (Website, Api, Console).
     * Create .php file in app src directory.
     */
    protected function createPhpApp()
    {
        $this->output('Creating app file.');

        /**
         * Build path and content.
         */
        $path = path('apps') . $this->app . path('ds') . 'src' . path('ds') . ucfirst($this->app) . '.php';
        $content = '<?php

use Pckg\Framework\Application\Website;

class ' . ucfirst($this->app) . ' extends Website
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
        $hosts = [];
        do {
            $host = $this->askQuestion('Enter hostname:');
        } while (($host && $hosts[] = $host) || $host);

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
        $dependencies = [];
        do {
            $dependency = $this->askQuestion('Enter composer dependency:');
        } while (($dependency && $dependencies[] = $dependency) || $dependency);

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
        if (!$this->askConfirmation('Do you want to commit changes?')) {
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
