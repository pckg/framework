<?php namespace Pckg\Framework\Inter\Entity\Helper;

use Pckg\Framework\Inter\Entity\Languages;

trait Language
{

    public function language()
    {
        return $this->belongsTo(Languages::class)
                    ->foreignKey('language_id');
    }

}
