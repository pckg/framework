<?php

namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;

trait AddDatabaseConnection
{
    /**
     * Ask user if he wants to add database config.
     * Ask for credentials.
     * Create database.php config.
     */
    protected function addDatabaseConnection()
    {
        if ($this->option('skip-database')) {
            return $this->output('Skipping database configuration');
        }

        if (!$this->askConfirmation('Do you want to add database connection?', false, '/^(y|yes)/i')) {
            return;
        }

        $connections = [];
        if ($connection = $this->argument('database')) {
            $connections[0] = [];
            list(
                $connections[0]['driver'],
                $connections[0]['host'],
                $connections[0]['user'],
                $connections[0]['password'],
                $connections[0]['database'],
                ) = explode(',', $connection);
        } else {
            do {
                $driver = $this->askChoice('Select database driver', ['mysql', 'faker'], 0);
                $host = $this->askQuestion('Host:');
                $user = $this->askQuestion('User:');
                $password = $this->askQuestion('Password:');
                $database = $this->askQuestion('Database:');
                $connections[] = [
                    'driver'   => $driver,
                    'host'     => $host,
                    'user'     => $user,
                    'password' => $password,
                    'database' => $database,
                ];
            } while ($this->askConfirmation('Do you want to add database connection?', false, '/^(y|yes)/i'));
        }

        $this->output('Adding database connections');
// @T00D00 - Add to database.php
        $this->output('Database connections added');
    }
}
