<?php namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Cookie extends Lazy
{

    const EXPIRATION = 2592000; // 30 days

    function __construct(&$_cookie = [])
    {
        if (empty($_cookie)) {
            $_cookie = $_COOKIE;
        }

        parent::__construct($_cookie);
    }

    public function set(
        $name,
        $value = '',
        $expiration = self::EXPIRATION,
        $path = null,
        $domain = null,
        $secure = false,
        $httponly = false
    ) {
        setcookie($name, $value, time() + $expiration, $path, $domain, $secure, $httponly);

        return $this;
    }

    public function delete($name)
    {
        setcookie($name, null, time() - static::EXPIRATION);

        return $this;
    }

}