<?php namespace Pckg\Framework\Console;

use Pckg\Concept\Reflect;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class Command extends SymfonyConsoleCommand
{

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param $data
     *
     * @return $this
     */
    public function executeManually($data)
    {
        /**
         * Create symfony application.
         */
        $application = new Application();

        /**
         * Add current command.
         */
        $application->add($this);
        $application->setAutoExit(false);

        /**
         * Prepare args.
         */
        $args = escapeshellargs($data);
        array_unshift($args, $this->getName());

        /**
         * Run command.
         */
        $application->run(new StringInput(implode(' ', $args)), new NullOutput());

        return $this;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        Reflect::method($this, 'handle');
    }

    protected function quit($msg = null)
    {
        exit($msg ? $this->output($msg) : $msg);
    }

    protected function getQuestionHelper()
    {
        return $this->getHelper('question');
    }

    public function addOptions($options, $mode = null)
    {
        foreach ($options as $name => $description) {
            $this->addOption($name, null, $mode, $description);
        }

        return $this;
    }

    public function addArguments($arguments, $mode = null)
    {
        foreach ($arguments as $name => $description) {
            $this->addArgument($name, $mode, $description, null);
        }

        return $this;
    }

    public function argument($name, $default = null)
    {
        return $this->input->getArgument($name) ?: $default;
    }

    public function option($name, $default = null)
    {
        return $this->input->getOption($name) ?? $default;
    }

    public function output($msg = '', $type = null) // info, comment, question, error
    {
        $this->output->write(($type ? '<' . $type . '>' : '') . $msg . "\n" . ($type ? '</' . $type . '>' : ''));

        return $this;
    }

    public function ask($question)
    {
        return $this->getQuestionHelper()->ask($this->input, $this->output, $question);
    }

    public function askQuestion($question, $default = null, $attempts = null, $validator = null)
    {
        $question = new Question('<question>' . $question . '</question>', $default);

        if ($attempts) {
            $question->setMaxAttempts($attempts);
        }

        if ($validator) {
            $question->setValidator($validator);
        }

        return $this->ask($question);
    }

    public function askConfirmation($question, $default = true, $trueAnswerRegex = '/^y/i')
    {
        return $this->ask(new ConfirmationQuestion('<question>' . $question . '</question>', $default,
                                                   $trueAnswerRegex));
    }

    public function askChoice($question, array $choices, $default = null)
    {
        return $this->ask(new ChoiceQuestion('<question>' . $question . '</question>', $choices, $default));
    }

    public function exec($execs, $printOutput = true, $cd = false)
    {
        if (!is_array($execs)) {
            $execs = [$execs];
        }

        $outputs = [];
        foreach ($execs as $help => $exec) {
            if (is_string($help)) {
                $this->output("\n" . $help);
            }

            $output = null;
            exec(($cd ? 'cd ' . (is_bool($cd) ? path('root') : $cd) . ' && ' : '') . $exec, $output);

            if ($printOutput) {
                $this->output(implode("\n", $output));
            } else {
                $outputs[] = $output;
            }
        }

        return $outputs;
    }

    protected function getApp()
    {
        return context()->getOrDefault('appName');
    }

}