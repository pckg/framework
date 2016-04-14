<?php namespace Pckg\Framework\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class CreatePckgProject
 *
 */
class CreatePckgProject extends Command
{

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

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->fetchAppName();
        $this->checkExistance();
        $this->createDirectories();
        $this->createPhpApp();
        $this->addToRouter();
        $this->addDatabaseConnection();
        $this->addComposerDependencies();
        $this->commitChanges();

        $this->output->write("App " . $this->app . ' created');
    }

    protected function fetchAppName()
    {
        $helper = $this->getHelper('question');

        $this->app = $this->input->getArgument('app');
        if (!$this->app) {
            $question = new Question('Name of app: ');
            $this->app = $helper->ask($this->input, $this->output, $question);
        }

        if (!$this->app) {
            exit("Empty answer");
        }
    }

    protected function checkExistance()
    {
        $path = path('apps') . $this->app;

        if (is_dir($path)) {
            exit('Path ' . $path . ' already exists');
        }
    }

    protected function createDirectories()
    {
        $path = path('apps') . $this->app . path('ds');

        mkdir($path, 0777);
        touch($path . '.gitkeep');
        mkdir($path . 'config', 0777);
        mkdir($path . 'src', 0777);
    }

    /**
     * Ask for parent (Website, Api, Console).
     * Create .php file in app src directory.
     */
    protected function createPhpApp()
    {

    }

    /**
     * Ask user if he wants to add it to router.
     * Ask for domains.
     * Alter router.
     */
    protected function addToRouter()
    {

    }

    /**
     * Ask user if he wants to add database config.
     * Ask for credentials.
     * Create database.php config.
     */
    protected function addDatabaseConnection()
    {

    }

    /**
     * Ask user for composer dependencies.
     * Require them.
     */
    protected function addComposerDependencies()
    {

    }

    /**
     * Ask user if he wants to commit changes.
     * Add created and changed files.
     */
    protected function commitChanges()
    {

    }

}
