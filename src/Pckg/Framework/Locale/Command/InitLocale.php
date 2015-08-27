<?php


namespace Pckg\Framework\Locale\Command;

use Pckg\Concept\AbstractChainOfReponsibility;


class InitLocale extends AbstractChainOfReponsibility
{

    public function execute()
    {
        $locale = 'sl_SI';

        setlocale(LC_ALL, $locale);
        setlocale(LC_TIME, $locale);

        $this->next->execute();
    }

}