<?php

namespace Pckg\Framework\Inter\Record;

use Pckg\Database\Record;
use Pckg\Framework\Inter\Entity\Languages;

class Language extends Record
{

    protected $entity = Languages::class;

    public function getCurrentRouteTranslation() {
        return json_encode(router()->get());
        return 'test';
    }

}