<?php namespace Pckg\Framework\Console;

use Pckg\Concept\Reflect;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
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
        return $this->input->getOption($name) ?: $default;
    }

    public function output($msg = '')
    {
        $this->output->write($msg . "\n");

        return $this;
    }

    public function ask($question)
    {
        return $this->getQuestionHelper()->ask($this->input, $this->output, $question);
    }

    public function askQuestion($question, $default = null)
    {
        return $this->ask(new Question($question, $default));
    }

    public function askConfirmation($question, $default = true, $trueAnswerRegex = '/^y/i')
    {
        return $this->ask(new ConfirmationQuestion($question, $default, $trueAnswerRegex));
    }

    public function askChoice($question, array $choices, $default = null)
    {
        return $this->ask(new ChoiceQuestion($question, $choices, $default));
    }

    public function exec($execs)
    {
        if (!is_array($execs)) {
            $execs = [$execs];
        }

        foreach ($execs as $exec) {
            exec($exec, $output);
            $this->output(implode("\n", $output));
        }
    }

    protected function getApp()
    {
        return $_SERVER['argv'][1];
    }

}