<?php namespace Pckg\Framework\Inter\Middleware;

class CatchUrlLanguageChange
{

    public function execute(callable $next)
    {
        if ($lang = get('lang')) {
            /**
             * @T00D00 - check lang existance
             */
            $_SESSION['pckg_dynamic_lang_id'] = get('lang');
            redirect();
        }

        return $next();
    }

}