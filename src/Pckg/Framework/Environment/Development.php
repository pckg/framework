<?php

namespace Pckg\Framework\Environment;

use DebugBar\DebugBar;
use DebugBar\StandardDebugBar;
use DebugBar\Storage\FileStorage;
use Exception;
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
        $this->debugBar->setStorage(new FileStorage('/tmp/debugbar_storage'));

        $context->bind(Config::class, $config);

        $this->init();

        $config->parseDir(path('root'));

        if (isset($_SERVER['REMOTE_ADDR']) && !in_array($_SERVER['REMOTE_ADDR'], config('pckg.framework.dev', []))) {
            die('Unauthorized for dev!');
        }
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
                $renderer = $this->debugBar->getJavascriptRenderer();

                $renderer->setOpenHandlerUrl('/open.php');
                $this->debugBar->sendDataInHeaders(true);

                return $renderer->renderHead() . $renderer->render();
            },
        ];
    }

}