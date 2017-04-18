<?php namespace Pckg\Framework\Inter\Middleware;

use Pckg\Framework\Request\Data\Session;

class CatchUrlLanguageChange
{

    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

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