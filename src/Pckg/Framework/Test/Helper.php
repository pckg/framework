<?php namespace Pckg\Framework\Test;

use Codeception\Configuration;
use Codeception\Module;
use Pckg\Database\Query;
use Pckg\Database\Repository;
use Pckg\Database\Repository\PDO as PDORepository;
use PDO;

class Helper extends Module
{

    public $sqls = [];

    public $queryPreparedListener;

    public function initPckg($dir)
    {
        $this->autoloadPath($dir);
        $this->loadPckg();
        $connection = $this->connectDatabase($this->getCodeceptConfig()['pckg']['database']['default']);
        $this->registerDatabase($connection, 'default');
    }

    public function getCodeceptConfig()
    {
        return Configuration::config();
    }

    public function loadPckg()
    {
        $this->loadApp(
            $this->getCodeceptConfig()['pckg']['application'],
            $this->getCodeceptConfig()['pckg']['platform']
        );
    }

    public function loadApp($app, $platform = null)
    {
        $pckg = include realpath(__DIR__ . '/../../../../../../../vendor/pckg/framework/src/bootstrap.php');
        $pckg($app, $platform);
    }

    public function autoloadPath($dir)
    {
        $dir = realpath($dir . '/../src');
        $autoloader = include realpath(__DIR__ . '/../../../../../../../vendor/autoload.php');
        $autoloader->add('', $dir, true);
    }

    public function connectDatabase($config = [])
    {
        $pdo = new PDO(
            "mysql:host=" . $config['host'] . ";charset=" . $config['charset'] . ";dbname=" . $config['db'],
            $config['user'],
            $config['pass']
        );

        $pdo->uniqueName = $config['host'] . "-" . $config['db'];

        return $pdo;
    }

    public function registerDatabase($connection, $repository)
    {
        $r = new PDORepository($connection, $repository);
        context()->bind(Repository::class . '.' . $repository, $r);
        if ($repository == 'default') {
            context()->bind(Repository::class, $r);
        }
    }

    public function emptyDatabase($connection)
    {

    }

    public function importDatabase($repository, $connection, $file)
    {

    }

    public function listenToQueries()
    {
        $this->sqls = [];

        $this->ignoreQueryListening();

        $CI = $this;
        $this->queryPreparedListener = dispatcher()->listen(
            Query::class . '.prepared',
            function($sql, $binds) use ($CI) {
                sort($binds);
                $CI->sqls[] = [
                    'sql'   => $sql,
                    'binds' => $binds,
                ];
            }
        );
    }

    public function getListenedQueries()
    {
        $sqls = $this->sqls;

        $this->sqls = [];

        $this->ignoreQueryListening();

        return $sqls;
    }

    public function ignoreQueryListening()
    {
        if ($this->queryPreparedListener) {
            dispatcher()->ignore(Query::class . '.prepared', $this->queryPreparedListener);
        }
    }

}