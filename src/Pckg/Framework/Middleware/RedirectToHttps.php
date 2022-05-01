<?php

namespace Pckg\Framework\Middleware;

/**
 * Class RedirectToHttps
 * @package Pckg\Framework\Middleware
 * @deprecated
 */
class RedirectToHttps
{
    public function execute(callable $next)
    {
        if (!isHttp() || request()->isSecure()) {
            return $next();
        }

        redirect('https://' . first(request()->getDomain(), config('domain')) . router()->getURL());
    }
}
