<?php

namespace Pckg\Framework\Console\Command;

use Pckg\Queue\Service\RabbitMQ;
use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class Sleep extends Command
{
    public function handle()
    {
        $sleep = $this->option('sleep', 1);
        $this->outputDated('Sleeping ' . $sleep . ' ...');
        sleep($sleep);
        $this->outputDated('Slept ...');

        $channel = 'test-201904041900';
        $this->outputDated('Connecting to RabbitMQ');

        $this->outputDated('Publishing message');

        $broker = $this->getRabbitMQ();
        $broker->makeShoutExchange($channel);
        $broker->shout($channel, ['event' => 'service:one:ready']);
        $broker->close();

        $this->outputDated('Closed connection');
    }

    protected function configure()
    {
        $this->setName('sleep:sleep')
             ->setDescription('Sleep')
             ->addOptions(['sleep' => 'Sleep'], InputOption::VALUE_REQUIRED);
    }

    /**
     * @return RabbitMQ
     */
    public function getRabbitMQ()
    {
        return resolve(RabbitMQ::class);
    }
}
