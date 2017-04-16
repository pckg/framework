<?php namespace Pckg\Framework\Inter\Entity;

use Pckg\Database\Entity;
use Pckg\Framework\Inter\Record\Language;

class Languages extends Entity
{

    protected $record = Language::class;

    public function boot()
    {
        $this->joinTranslations();
    }

}