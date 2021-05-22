<?php

namespace Pckg\Framework\Application\Command;

use Pckg\Concept\Event\Dispatcher;
use Pckg\Framework\Application;
use Pckg\Framework\Config;
use Pckg\Framework\Provider\Helper\Registrator;
use Pckg\Locale\Command\Localize;

class RegisterApplication
{
    use Registrator;

    protected Application $application;

    protected Config $config;

    protected Dispatcher $dispatcher;

    public function __construct(Application $application, Config $config, Dispatcher $dispatcher)
    {
        $this->application = $application;
        $this->config = $config;
        $this->dispatcher = $dispatcher;
    }

    public function execute(callable $next)
    {
        /**
         * Register main application provider.
         */
        $this->application->getProvider()->register();
        // 0.44 -> 0.97 / 1.03 = 0.53s = 50%

        /**
         * Parse application config.
         */
        $this->config->parseDir(path('app'));

        /**
         * Localize any config changes.
         */
        chain([Localize::class]);

        /**
         * Trigger event.
         */
        $this->dispatcher->trigger(Application::EVENT_REGISTERED);

        return $next();
    }
}
