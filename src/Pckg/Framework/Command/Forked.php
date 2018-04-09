<?php namespace Pckg\Framework\Command;

use Pckg\Database\Repository;

class Forked
{

    public function handle()
    {
        foreach (config('database') as $name => $config) {
            $repository = context()->getOrDefault(Repository::class . '.' . $name);
            if (!$repository) {
                continue;
            }

            if (!method_exists($repository, '__wakeup')) {
                continue;
            }

            $repository->__wakeup();
        }
    }

}