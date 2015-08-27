<?php

namespace Pckg\Application;

use Pckg\Application;

class Api extends Application
{

    protected $initChain = [
        'InitConfig',
        'InitLocale',
        'InitDatabase',
        'InitRouter',
        'InitSession',
        'InitResponse',
        'InitRequest',
        'InitI18n',
    ];

    protected $runChain = [
        'RunRequest',
        'RunResponse'
    ];

    public function run()
    {
        $this->middleware();

        return parent::run();
    }

    public function assets()
    {
        return [];
    }

}