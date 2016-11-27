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
        $this->loadApp($this->getCodeceptConfig()['pckg']['application']);
    }

    public function loadApp($app)
    {
        $pckg = include realpath(__DIR__ . '/../../../../../../../vendor/pckg/framework/src/bootstrap.php');
        $pckg($app);
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

    public function importDatabase($filename)
    {
        $repository = context()->get(Repository::class);

        $prepare = $repository->prepareSQL('DROP DATABASE pckg_database');
        $repository->executePrepared($prepare);

        $prepare = $repository->prepareSQL('CREATE DATABASE pckg_database');
        $repository->executePrepared($prepare);

        $prepare = $repository->prepareSQL('USE pckg_database');
        $repository->executePrepared($prepare);

        $templine = '';
        $lines = file($filename);
        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '/*' || substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            $templine .= $line;
            if (substr(trim($line), -1, 1) == ';') {
                $prepare = $repository->prepareSQL($templine);
                $repository->executePrepared($prepare);
                $templine = '';
            }
        }
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