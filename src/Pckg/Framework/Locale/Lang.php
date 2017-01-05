<?php namespace Pckg\Framework\Locale;

use Pckg\Database\Entity\Extension\Adapter\Lang as LangAdapter;
use Pckg\Framework\Inter\Entity\Languages;

class Lang implements LangAdapter
{

    protected $langId = 'en';

    public function setLangId($langId)
    {
        $this->langId = $langId;

        return $this;
    }

    public function langId($section = null)
    {
        return $this->langId;
    }

    public function getLanguages()
    {
        return (new Languages())->all();
    }

}
