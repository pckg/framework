<?php

namespace Pckg\Config\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Config;
use Pckg\Context;

class InitConfig extends AbstractChainOfReponsibility
{

    public function __construct(Config $config, Context $context)
    {
        $this->config = $config;
        $this->context = $context;
    }

    public function execute()
    {
        $this->config->initSettings();
        $this->config->parseDir(path('app'), $this);

        $this->next->execute();
    }

}