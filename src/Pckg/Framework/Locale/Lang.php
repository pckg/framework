<?php

namespace Pckg\Framework\Locale;

use Pckg\Database\Entity\Extension\Adapter\Lang as LangAdapter;

class Lang implements LangAdapter{

    public function langId()
    {
        return 'en';
    }

}
