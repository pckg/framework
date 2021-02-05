<?php

namespace Pckg\Framework;

use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\Provider\Helper\Registrator;

abstract class Application
{

    use Registrator;

    protected $provider;

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
         * This will parse config, set localization 'things', estamblish connection to database, initialize and register
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

    abstract public function inits();

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

    abstract public function runs();

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
