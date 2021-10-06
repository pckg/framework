<?php

namespace Pckg\Framework;

use Pckg\Framework\Config\Command\InitConfig;
use Pckg\Framework\Provider\Helper\Registrator;

class Application
{
    use Registrator;

    protected $provider;

    const EVENT_REGISTERED = self::class . '.registered';

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function initAndRun()
    {
        /**
         * Initialize application.
         * This will parse config, set localization 'things', establish connection to database, initialize and register
         * routes, set application autoloaders and providers, session, response, request and assets.
         */
        measure('Initializing ' . static::class, function () {
            $this->init(); // 0.37s -> 0.94s / 1.03s = 57%
        });

        /**
         * Run applications.
         * Everything was preset, we need to run command or request and return response.
         */
        measure('Running ' . static::class, function () {
            $this->run();
        });
    }

    public function inits()
    {
        return [];
    }

    public function init()
    {
        trigger(Application::class . '.initializing', [$this]);

        $init = chain($this->inits(), 'execute', [$this]);

        if (!$init) {
            throw new \Exception('Error initializing application');
        }

        trigger(Application::class . '.initialized', [$this]);

        return $this;
    }

    public function runs()
    {
        return [];
    }

    public function run()
    {
        trigger(Application::class . '.running', [$this]);

        $run = chain($this->runs(), 'execute', [$this]);

        if (!$run) {
            throw new \Exception('Error running application');
        }

        trigger(Application::class . '.ran', [$this]);

        return $this;
    }
}
