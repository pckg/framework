<?php namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Cookie extends Lazy
{

    const EXPIRATION = 2592000; // 30 days

    const DURATION_MONTH = 2592000; // 30 days

    public function __construct(array $arr = [])
    {
        parent::__construct($_COOKIE);
    }

    public function set(
        $name,
        $value = '',
        $expiration = self::DURATION_MONTH,
        $path = '/',
        $domain = ''
    ) {
        $time = time() + $expiration;
        $domain = '';

        if (PHP_VERSION_ID >= 70300) {
            setcookie($name, $value, [
                'expires' => $time,
                'path' => $path,
                'secure' => true,
                'domain' => $domain,
                'samesite' => 'strict', // httponly?
            ]);
            return;
        } else {
            setcookie($name, $value, $time, $path . '; samesite=strict', $domain, true, true);
        }

        $_COOKIE[$name] = $value;

        return $this;
    }

    public function delete($name)
    {
        $this->set($name, null, -1 * static::EXPIRATION);

        return $this;
    }

}