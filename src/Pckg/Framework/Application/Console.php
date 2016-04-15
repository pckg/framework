<?php namespace Pckg\Framework\Application;

use Pckg\Framework\Application;
use Pckg\Framework\Application\Console\Command\Init;
use Pckg\Framework\Application\Console\Command\Run;
use Pckg\Framework\Console\Command\ClearCache;
use Pckg\Framework\Console\Command\CreatePckgProject;

class Console extends Application
{

    protected $application;

    public function __construct(Application $application = null)
    {
        $this->application = $application ?: $this;
    }

    /**
     * @return Website
     */
    public function getApplication()
    {
        return $this->application;
    }

    public function inits()
    {
        return [
            Init::class,
        ];
    }

    public function runs()
    {
        return [
            Run::class,
        ];
    }

    public function consoles()
    {
        return [
            CreatePckgProject::class,
            ClearCache::class,
        ];
    }

}