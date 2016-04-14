<?php namespace Pckg\Framework\Console;

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
        $this->handle();
    }

    protected function quit($msg = null)
    {
        exit($msg ? $this->output($msg) : $msg);
    }

    protected function getQuestionHelper()
    {
        return $this->getHelper('question');
    }

    public function output($msg)
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

}