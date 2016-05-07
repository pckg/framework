<?php

namespace Pckg\Framework\Locale;

use Pckg\Database\Entity\Extension\Adapter\Lang as LangAdapter;

class Lang implements LangAdapter
{

    protected $langId = 'en';

    public function setLangId($langId)
    {
        $this->langId = $langId;

        return $this;
    }

    public function langId()
    {
        return $this->langId;
    }

}
