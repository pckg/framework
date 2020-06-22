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
        $expiration = self::EXPIRATION
    ) {
        setcookie($name, $value, [
            'expires' => time() + $expiration,
            'path' => $path,
            'secure' => true,
            'samesite' => 'strict'
        ]);
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