<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;
use Pckg\Framework\Request\Data\SessionDriver\Db;
use Pckg\Framework\Request\Data\SessionDriver\FileDriver;

class Session extends Lazy
{

    /**
     * @var Db
     */
    protected $driver;

    public function __construct(array $arr = [])
    {
        $driver = config('pckg.session.driver', FileDriver::class);

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