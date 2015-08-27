<?php

namespace Pckg\Helper;

class Assets
{
    private static $assets = [];

    static function add($assets)
    {
        if (!is_array($assets))
            $assets = (array)$assets;

        self::addAssets($assets);
    }

    static function addAssets($assets, $allowDouble = false)
    {
        foreach ($assets AS $asset) {
            self::addAsset($asset, $allowDouble);
        }
    }

    static function addAsset($path, $allowDouble = false)
    {
        if ($allowDouble || !in_array($path . path('ds') . $asset, self::$assets))
            self::$assets[] = $path . path('ds') . $asset;
    }

    static function get()
    {
        return self::$assets;
    }
}

?>