<?php

namespace Pckg\Framework\Environment;

use DebugBar\DebugBar;
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

        $context->bind(DebugBar::class, $this->debugBar = new StandardDebugBar());
        $this->debugBar->setStorage(new \DebugBar\Storage\FileStorage('/tmp/debugbar_storage'));
        if ((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(
                                                              $_SERVER['HTTP_X_REQUESTED_WITH']
                                                          ) == 'xmlhttprequest') || isset($_POST['ajax'])
        ) {
            //$this->debugBar->sendDataInHeaders();
        } else {
            //$this->debugBar->sendDataInHeaders(true);
        }

        $context->bind(Config::class, $config);

        $this->init();

        $config->parseDir(path('root'));
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
            function() {
                return '';
                $renderer = $this->debugBar->getJavascriptRenderer();

                return $renderer->renderHead() . $renderer->render();
            },
        ];
    }

}