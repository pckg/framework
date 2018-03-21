<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;
use Pckg\Framework\Request\Data\SessionDriver\Db;
use SessionHandler;

class Session extends Lazy
{

    /**
     * @var Db
     */
    protected $driver;

    public function __construct(array $arr = [])
    {
        $driver = config('pckg.session.driver', SessionHandler::class);

        $this->driver = new $driver;
    }

    function __destruct()
    {
        /**
         * Fix issue first!
         * Is it fixed?
         */
    }

}

?>