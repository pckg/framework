<?php

namespace Pckg\Framework\Helper;

use Exception;
use Pckg\Concept\Context as ConceptContext;
use Pckg\Concept\Reflect;
use Pckg\Framework\Application;
use Pckg\Framework\Application\Console;
use Pckg\Framework\Application\Website;
use Pckg\Framework\Console\Provider\Console as ConsoleProvider;
use Pckg\Framework\Environment;
use Pckg\Framework\Provider\Helper\Registrator;
use Pckg\Framework\Reflect\FrameworkResolver;
use Pckg\Htmlbuilder\Resolver\FormResolver;
use Symfony\Component\Console\Application as SymfonyConsole;

class Context extends ConceptContext
{
    use Registrator;

    public function boot($environment, $run = true, $app = null)
    {
        /**
         * Create development environment.
         * We automatically display errors and load debugbar.
         * Exceptions are caught in method.
         */
        $env = $this->createEnvironment($environment);

        /**
         * ./config/ folder is now parsed, we can continue with execution.
         * Create application by environment.
         */
        $application = $env->createApplication(context(), $app);

        /**
         * Initialize and run application.
         */
        if ($run) {
            $application->initAndRun();
        } else {
            $application->init();
        }

        return $application;
    }

    /**
     * @return Environment
     */
    public function createEnvironment($environment)
    {
        try {
            $env = Reflect::create($environment);
            $this->bind(Environment::class, $env);
            $env->register();
            return $env;
        } catch (\Throwable $e) {
            error_log('Error registering environment: ' . $e->getMessage());
            die('Error registering environment');
        }
    }

    public static function createInstance()
    {
        /**
         * @T00D00 - Simplify this.
         */
        Reflect::prependResolver(new Reflect\Resolver\Context());
        Reflect::prependResolver(new FrameworkResolver());
        Reflect::prependResolver(new FormResolver());

        $instance = parent::createInstance();

        return $instance;
    }
}
