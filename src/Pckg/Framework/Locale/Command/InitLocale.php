<?php

namespace Pckg\Framework\Locale\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Context;
use Pckg\Framework\Locale\Lang;

class InitLocale extends AbstractChainOfReponsibility
{

    protected $context;

    public function __construct(Context $context){
        $this->context = $context;
    }

    public function execute(callable $next)
    {
        $locale = 'sl_SI';

        setlocale(LC_ALL, $locale);
        setlocale(LC_TIME, $locale);

        $this->context->bind('Lang', new Lang());

        return $next();
    }

}