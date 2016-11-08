<?php namespace Pckg\Framework\Application\Command;

use Pckg\Framework\Application;
use Pckg\Framework\Locale\Command\Localize;
use Pckg\Framework\Provider\Helper\Registrator;

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
        $this->application->getProvider()->register();
        config()->parseDir(path('app'));
        chain([Localize::class]);

        return $next();
    }

}