<?php namespace Pckg\Framework\Console;

use Pckg\Framework\Console\Command\AddDatabaseConnection;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

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

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

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

        $path = path('apps') . $this->app . path('ds');

        mkdir($path, 0777);
        mkdir($path . 'config', 0777);
        mkdir($path . 'src', 0777);
        touch($path . '.gitkeep');

        $this->output('Directories created.');
    }

    /**
     * Ask for parent (Website, Api, Console).
     * Create .php file in app src directory.
     */
    protected function createPhpApp()
    {
        if (($parent = $this->askChoice('Select application type:', ['skip', 'Website', 'Api', 'Console'],
                0)) == 'skip'
        ) {
            return $this->output('Skipping app file creation.');
        }

        $path = path('apps') . $this->app . path('ds') . 'src' . path('ds') . ucfirst($this->app) . '.php';
        $content = '<?php

use Pckg\Framework\Application\\' . $parent . ';

class ' . ucfirst($this->app) . ' extends ' . $parent . '
{

}
';

        $this->output('Creating app file.');
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
         * @T00D00 - Add to router
         *  - Read config/router.php
         *  - Add hosts to apps.$appname.host[]
         */
        $this->output('Global router has been updated.');
    }

    /**
     * Ask user for composer dependencies.
     * Require them.
     */
    protected function addComposerDependencies()
    {
        if (!$this->askConfirmation('Do you want to add composer dependencies?')) {
            return $this->output('Skipping dependency requirements.');
        }

        $dependencies = [];
        do {
            $dependency = $this->askQuestion('Enter dependency:');
        } while (($dependency && $dependencies[] = $dependency) || $dependency);

        $this->output('Requiring dependencies.');
        /**
         * @T00D00 - Require dependencies
         *  - Execute composer require $dependency
         */
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
         * @T00D00 - Commit changes
         *  - Add changed files
         *  - Commit with message 'Creating app $appname'
         */
        $this->output('Changes commited.');
    }

    protected function finishCreation()
    {
        return $this->output("App " . $this->app . ' created');
    }

}
