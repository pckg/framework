<?php

namespace Pckg\Framework\Environment;

use DebugBar\StandardDebugBar;
use Pckg\Concept\Context;
use Pckg\Framework\Config;
use Pckg\Framework\Environment;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Development extends Environment
{

    public $env = 'dev';

    protected $urlPrefix = '/dev.php';

    protected $context;

    /**
     * @var StandardDebugBar
     */
    protected $debugBar;

    function __construct(Config $config, Context $context)
    {
        error_reporting(E_ALL);
        ini_set("display_errors", "1");

        $this->context = $context;

        $this->registerExceptionHandler();

        $context->bind('DebugBar', $this->debugBar = new StandardDebugBar());
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

    public function assets()
    {
        return [
            function () {
                $renderer = $this->debugBar->getJavascriptRenderer();

                return $renderer->renderHead() . $renderer->render();
            },
        ];
    }

}