<?php

namespace Pckg\Framework\Test;

use Codeception\Configuration;
use Codeception\Module;
use Pckg\Database\Query;
use Pckg\Database\Repository;
use Pckg\Database\Repository\PDO as PDORepository;
use PDO;

/**
 * Class Helper
 * @package Pckg\Framework\Test
 * @deprecated ?
 */
class Helper extends Module
{

    public $sqls = [];

    public $queryPreparedListener;

    public function initConfigDatabases()
    {
        foreach ($this->getCodeceptConfig()['pckg']['database'] ?? [] as $name => $config) {
            Repository\RepositoryFactory::createRepositoryConnection($config, $name);
        }
    }

    public function initPckg($dir)
    {
        $this->autoloadPath($dir);
        $this->loadPckg();
        $this->initConfigDatabases();
    }

    public function initPartialPckg($dir)
    {
        $this->autoloadPath($dir);
        $this->loadApp();
        $this->initConfigDatabases();
    }

    public function getCodeceptConfig()
    {
        return Configuration::config();
    }

    public function loadPckg()
    {
        $this->loadApp($this->getCodeceptConfig()['pckg']['application']);
    }

    public function loadApp($app = null)
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
        $databaseName = $this->getCodeceptConfig()['pckg']['database']['default']['db'];

        $prepare = $repository->prepareSQL('DROP DATABASE ' . $databaseName);
        $repository->executePrepared($prepare);

        $prepare = $repository->prepareSQL('CREATE DATABASE ' . $databaseName);
        $repository->executePrepared($prepare);

        $prepare = $repository->prepareSQL('USE ' . $databaseName);
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

    public function listenToQueries($type = null, $sort = false)
    {
        $this->sqls = [];

        $this->ignoreQueryListening($type);

        $CI = $this;
        $this->queryPreparedListener = dispatcher()->listen(
            Query::class . '.prepared' . $type,
            function ($sql, $binds, $repo = null) use ($CI, $sort) {
                if ($sort) {
                    sort($binds);
                }
                $data = [
                    'sql'   => $sql,
                    'binds' => $binds,
                ];
                if ($repo) {
                    $data['repo'] = $repo;
                }
                $CI->sqls[] = $data;
            }
        );
    }

    public function getListenedQueries($type = null)
    {
        $sqls = $this->sqls;

        $this->sqls = [];

        $this->ignoreQueryListening($type);

        return $sqls;
    }

    public function ignoreQueryListening($type = null)
    {
        if ($this->queryPreparedListener) {
            dispatcher()->ignore(Query::class . '.prepared' . $type, $this->queryPreparedListener);
        }
    }
}
