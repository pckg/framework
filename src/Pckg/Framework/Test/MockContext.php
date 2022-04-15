<?php

namespace Pckg\Framework\Test;

use Pckg\Concept\Context;
use Pckg\Framework\Stack;

trait MockContext
{
    /**
     * @return mixed
     */
    protected function getPckgBootstrap()
    {
        $file = 'vendor/pckg/framework/src/bootstrap.php';
        if (!is_file($file)) {
            $file = 'src/bootstrap.php';
        }
        return include $file;
    }

    protected function mockContext(): \Pckg\Framework\Helper\Context
    {
        /**
         * Make sure that App is fully loaded?
         * We would like to mock the environment, application and request.
         */
        $bootstrap = $this->getPckgBootstrap();

        /**
         * Only bootstrap and create context. Do not create environment or init the application.
         * @var \Pckg\Concept\Context|\Pckg\Framework\Helper\Context $context
         */
        $originalContext = context();
        Stack::$providers = [];
        $this->context = $context = $bootstrap(null, null);

        $originalContext->bind(Context::class, $context);
        $originalContext->bind(\Pckg\Framework\Helper\Context::class, $context);

        return $context;
    }
}
