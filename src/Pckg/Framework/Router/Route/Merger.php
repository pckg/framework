<?php namespace Pckg\Framework\Router\Route;

trait Merger
{

    protected function mergeData($to, $from)
    {
        /**
         * We will return only defined keys.
         */
        $keys = array_union(array_keys($from), array_keys($to));
        $data = [];

        /**
         * Get selected / merged values.
         */
        foreach ($keys as $key) {
            $data[$key] = $from[$key] ?? ($to[$key] ?? null);
        }

        /**
         * Handle prefixes.
         */
        $prefixes = ['url' => '', 'name' => '.'];
        foreach ($prefixes as $prefixed => $separator) {
            $prefix = '';
            if (array_key_exists($prefixed . 'Prefix', $from)) {
                $prefix .= $from[$prefixed . 'Prefix'];
            }
            if (array_key_exists($prefixed . 'Prefix', $to)) {
                $prefix .= $to[$prefixed . 'Prefix'];
            }
            $data[$prefixed . 'Prefix'] = $prefix;
            if (array_key_exists($prefixed, $data)) {
                $realSeparator = $separator;
                if ($separator && strpos($data[$prefixed], $separator) === 0) {
                    $realSeparator = '';
                }
                if (!$data[$prefixed]) {
                    $realSeparator = '';
                }
                $data[$prefixed] = trim($prefix . $realSeparator . $data[$prefixed], $realSeparator);
            }
        }

        /**
         * Set empty prefixed values.
         */
        foreach ($data as $key => $val) {
            if (strpos($key, 'Prefix') && !array_key_exists(substr($key, 0, -6), $data)) {
                $data[substr($key, 0, -6)] = $val;
            }
        }

        return $data;
    }

}