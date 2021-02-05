<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Request\Data\SessionDriver\Db;
use Pckg\Framework\Request\Data\SessionDriver\FileDriver;

class Session
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
}
