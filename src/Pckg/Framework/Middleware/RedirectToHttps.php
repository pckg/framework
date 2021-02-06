<?php

namespace Pckg\Framework\Middleware;

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
