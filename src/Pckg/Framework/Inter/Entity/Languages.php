<?php namespace Pckg\Framework\Inter\Entity;

use Pckg\Database\Entity;
use Pckg\Database\Entity\Extension\Translatable;
use Pckg\Framework\Inter\Record\Language;

class Languages extends Entity
{

    use Translatable;

    protected $record = Language::class;

    public function boot()
    {
        $this->joinTranslations();
    }

}