<?php

namespace Pckg\Framework\Provider;

use Pckg\Framework\Provider;

/**
 * @deprecated
 */
class Frontend extends Provider
{
    public function assets()
    {
        return [
            'libraries' => [
                'js/http.compiled.js',
            ],
        ];
    }
}
