<?php

namespace Pckg\Framework\Inter\Record;

use Pckg\Database\Record;
use Pckg\Framework\Inter\Entity\Languages;

class Language extends Record
{

    protected $entity = Languages::class;

    public function getRootUrl()
    {
        return '/' . $this->slug . '/';
    }

}