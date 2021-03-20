<?php

namespace Pckg\Framework\Test;

use Pckg\Database\Repository;
use Pckg\Framework\Application\Command\InitDatabase;

trait Transactions
{

    protected $transactions = [];

    /**
     * @var MockRequest
     */
    protected $mock;

    public function startTransactions()
    {
        return $this;
    }

    public function dropTransactions()
    {
        return $this;
    }

    public function _beforeTransactionsExtension()
    {
        /**
         * Start transactions in all database.
         * How to prevent committing them?
         *
         * Mock framework.
         * Create application.
         * Init application, don't run it.
         */
        $this->mock = $this->mock()->initApp();

        $repository = Repository\RepositoryFactory::getOrCreateRepository('default');
        $repository->getConnection()->beginTransaction();
        $this->transactions[] = $repository;
    }

    public function _afterTransactionsExtension()
    {
        /**
         * Drop all transactions.
         */
        foreach ($this->transactions as $repository) {
            $repository->getConnection()->rollBack();
        }
    }

}
