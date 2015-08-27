<?php

namespace Pckg\Framework\Helper;

class JSON
{
    public static function from($json, $htmlEncode = FALSE)
    {
        return !$htmlEncode ? json_decode($json) : htmlspecialchars_decode(json_decode($json));
    }

    public static function to($json, $htmlEncode = FALSE)
    {
        return !$htmlEncode ? json_encode($json) : htmlspecialchars(json_encode($json));
    }
}

?>