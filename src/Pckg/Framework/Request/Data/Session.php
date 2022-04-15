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

    protected $source;

    public function __construct(array $arr = [])
    {
        $driver = config('pckg.session.driver', FileDriver::class);

        $this->driver = new $driver($arr);
    }

    public function setFromGlobals()
    {
        $this->setData($_SESSION);

        return $this;
    }

    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;

        return $this;
    }

    public function delete($key)
    {
        unset($_SESSION[$key]);

        return $this;
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function getDriver()
    {
        return $this->driver;
    }
}
