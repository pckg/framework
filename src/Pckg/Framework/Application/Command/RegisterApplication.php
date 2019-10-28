<?php namespace Pckg\Framework\Application\Command;

use Pckg\Framework\Application;
use Pckg\Framework\Provider\Helper\Registrator;
use Pckg\Locale\Command\Localize;

class RegisterApplication
{

    use Registrator;

    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function execute(callable $next)
    {
        /**
         * Register main application provider.
         */
        $this->application->getProvider()->register(); // 0.44 -> 0.97 / 1.03 = 0.53s = 50%

        /**
         * Parse application config.
         */
        config()->parseDir(path('app'));

        /**
         * Localize any config changes.
         */
        chain([Localize::class]);

        /**
         * Trigger event.
         */
        trigger(Application::class . '.registered');

        return $next();
    }

}