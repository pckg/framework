<?php

namespace Pckg\Framework\Middleware;

/**
 * Class RedirectToHttps
 * @package Pckg\Framework\Middleware
 * @deprecated
 */
class RedirectToHttps
{

    public function execute()
    {
        if (!isHttp() || request()->isSecure()) {
            return;
        }

        redirect('https://' . first(request()->getDomain(), config('domain')) . router()->getURL());
    }
}
