<?php namespace Pckg\Framework\Request\Session\Entity;

use Pckg\Database\Entity;
use Pckg\Framework\Request\Session\Record\Session;

class Sessions extends Entity
{

    protected $record = Session::class;

}