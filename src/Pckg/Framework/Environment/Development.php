<?php

namespace Pckg\Framework\Environment;

use DebugBar\DebugBar;
use DebugBar\StandardDebugBar;
use DebugBar\Storage\FileStorage;
use Pckg\Framework\Environment;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Development extends Production
{

    public $env = 'dev';

    protected $urlPrefix = '/dev.php';

    /**
     * @var StandardDebugBar
     */
    protected $debugBar;

    public function register()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", "1");

        $this->config->parseDir(BASE_PATH);

        if (false && isHttp() && !implicitDev()) {
            die('Unauthorized for dev!');
            exit;
        }

        $this->registerExceptionHandler();

        $this->context->bind(DebugBar::class, $this->debugBar = new StandardDebugBar());
        $this->debugBar->setStorage(new FileStorage('/tmp/debugbar_storage'));

        $this->init();
    }

    public function registerExceptionHandler()
    {
        $whoops = new Run();
        $whoops->pushHandler(function (\Throwable $e) {
            @error_log(exception($e));
        });
        $whoops->pushHandler(new PrettyPageHandler());
        $whoops->register();
    }

    public function assets()
    {
        return [];

        return [
            function () {
                $renderer = $this->debugBar->getJavascriptRenderer();

                $renderer->setOpenHandlerUrl('/open.php');
                $this->debugBar->sendDataInHeaders(true);

                return $renderer->renderHead() . $renderer->render();
            },
        ];
    }
}
