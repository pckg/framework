<?php

namespace Pckg\Framework\Console;

use Pckg\Concept\Reflect;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
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


    protected function prepareForExecution()
    {
    }

    /**
     * @return $this|bool
     */
    public function executeManually($data = [])
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
        array_unshift($data, $this->getName());
        $output = isConsole() ? new ConsoleOutput() : new BufferedOutput();
        $ok = $application->run(
            new ArrayInput($data),
            $output
        );
        if ($ok !== 0) {
            if ($output instanceof BufferedOutput) {
                error_log($output->fetch());
            }
            throw new \Exception('Cannot execute command ' . get_class($this));
        }

        return $ok == 0;
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
        try {
            Reflect::method($this, 'handle');
            return 0;
        } catch (\Throwable $e) {
            error_log(exception($e));
            return 1;
        }
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
        return $this->input->hasArgument($name) ? $this->input->getArgument($name) : $default;
    }

    public function option($name, $default = null)
    {
        return $this->input->hasOption($name) ? ($this->input->getOption($name) ?? $default) : $default;
    }

    public function output($msg = '', $type = null)
    {
        $this->output->write(($type ? '<' . $type . '>' : '') . $msg . ($type ? '</' . $type . '>' : '') . "\n");
        return $this;
    }

    public function outputDated($msg = '', $type = null)
    {
        return $this->output(date('Y-m-d H:i:s') . ' - ' . $msg, $type);
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
        return $this->ask(new ConfirmationQuestion(
            '<question>' . $question . '</question>',
            $default,
            $trueAnswerRegex
        ));
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

    public function decodeOption($name, $assoc = false)
    {
        $option = $this->option($name);
        return json_decode($option, $assoc);
    }

    /**
     * @param string $option
     * @param array  $keys
     *
     * @return array
     */
    protected function unpackOption(string $option, array $keys)
    {
        /**
         * JSON decode option
         */
        $decoded = $this->decodeOption($option, true);
/**
         * Build new array.
         */
        $collected = [];
        foreach ($keys as $key) {
            $collected[] = $decoded[$key];
        }

        return $collected;
    }

    /**
     * @param \Throwable $e
     */
    protected function outputException(\Throwable $e)
    {
        $this->outputDated(exception($e), 'error');
    }

    public function deserializeOption($name)
    {
        $option = $this->option($name);
        return $user = unserialize(base64_decode($option));
    }

    public function serializeOption($data)
    {
        return base64_encode(serialize($data));
    }

    public function onTerminate(callable $callable, $signals = [SIGTERM, SIGHUP, SIGUSR1])
    {
        foreach ($signals as $signal) {
            pcntl_signal($signal, $callable);
        }
/*
        $h = function($signo)
        {
            d("signal", $signo);
            switch ($signo) {
                case SIGTERM:
                    // handle shutdown tasks
                    d('sigterm');
                    exit;
                    break;
                case SIGHUP:
                    // handle restart tasks
                    d('sighup');
                    break;
                case SIGUSR1:
                    echo "Caught SIGUSR1...\n";
                    break;
                default:
                    // handle all other signals
            }

        };

        echo "Installing signal handler...\n";

// setup signal handlers
        pcntl_signal(SIGTERM, $h);
        pcntl_signal(SIGHUP,  $h);
        pcntl_signal(SIGUSR1, $h);*/
    }
}
