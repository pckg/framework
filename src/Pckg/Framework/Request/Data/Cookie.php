<?php namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Cookie extends Lazy
{

    const EXPIRATION = 2592000; // 30 days

    public function __construct(array $arr = [])
    {
        parent::__construct($_COOKIE);
    }

    public function set(
        $name,
        $value = '',
        $expiration = self::EXPIRATION,
        //$path = '/; samesite=strict',
        $path = '',
        $domain = null,
        $secure = true,
        $httponly = true
    ) {
        setcookie($name, $value, time() + $expiration, $path, $domain, $secure, $httponly);
        $_COOKIE[$name] = $value;

        return $this;
    }

    public function delete($name)
    {
        //setcookie($name, null, time() - static::EXPIRATION, '/; samesite=strict', '', true, true);
        setcookie($name, null, time() - static::EXPIRATION, '', '', true, true);
        unset($_COOKIE[$name]);

        return $this;
    }

}