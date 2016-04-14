<?php namespace Pckg\Framework\Application;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Application;
use Pckg\Framework\Provider\Helper\Registrator;

class RegisterApplication extends AbstractChainOfReponsibility
{

    use Registrator;

    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function execute(callable $next)
    {
        $this->registerRoutes($this->application->routes());
        $this->registerAutoloaders($this->application->autoload(), $this->application);
        $this->registerProviders($this->application->providers(), $this->application);
        $this->registerConsoles($this->application->consoles(), $this->application);

        return $next();
    }

}