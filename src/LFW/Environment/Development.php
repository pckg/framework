<?php

namespace Pckg\Environment;

use DebugBar\StandardDebugBar;
use Exception;
use Pckg\Config;
use Pckg\Context;
use Pckg\Environment;
use Pckg\Environment\Command\DefinePaths;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Development extends Environment
{

    public $env = 'dev';

    protected $urlPrefix = '/dev.php';

    protected $context;

    protected $initChain = [
        DefinePaths::class,
    ];

    function __construct(Config $config, Context $context)
    {
        error_reporting(E_ALL);
        ini_set("display_errors", "1");

        $this->context = $context;

        $this->registerExceptionHandler();

        $context->bind('DebugBar', new StandardDebugBar());
        $context->bind('Config', $config);

        $this->init();

        $config->parseDir(path('root'), $this);
    }

    public function registerExceptionHandler()
    {
        $whoops = new Run;
        $whoops->pushHandler(new PrettyPageHandler);
        $whoops->register();
    }

}