<?php

namespace Pckg\Inter\Entity;

use Pckg\Database\Entity;
use Pckg\Database\Entity\Extension\Translatable;
use Pckg\Inter\Record\Language;

class Languages extends Entity {

    use Translatable;

    protected $record = Language::class;

}