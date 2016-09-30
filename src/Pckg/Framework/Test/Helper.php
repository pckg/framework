<?php namespace Pckg\Framework\Test;

use Codeception\Configuration;
use Codeception\Module;

class Helper extends Module
{

    public function loadPckg()
    {
        $config = Configuration::config();
        $this->loadApp($config['pckg']['application'], $config['pckg']['platform']);
    }

    public function loadApp($app, $platform = null)
    {
        $pckg = include realpath(__DIR__ . '/../../../../../../../vendor/pckg/framework/src/bootstrap.php');
        $pckg($app, $platform);
    }

    public function connectDatabase($config = [])
    {

    }

    public function registerDatabase($connection, $repository)
    {

    }

    public function emptyDatabase($connection)
    {

    }

    public function importDatabase($repository, $connection, $file)
    {

    }

}