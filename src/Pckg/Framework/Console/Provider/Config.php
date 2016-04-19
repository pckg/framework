<?php namespace Pckg\Framework\Console\Provider;

use Pckg\Framework\Console\Command\CreatePckgProject;
use Pckg\Framework\Provider;

class Config extends Provider
{

    public function consoles()
    {
        return [
            CreatePckgProject::class,
        ];
    }

}