<?php namespace Pckg\Framework\Provider;

use Pckg\Framework\Provider;

class Frontend extends Provider
{

    public function assets()
    {
        return [
            'main' => [
                'js/http.compiled.js',
            ],
        ];
    }

}