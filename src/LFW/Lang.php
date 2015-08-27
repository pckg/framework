<?php

namespace Pckg;

use Exception;

class Lang
{
    const EN = 'en';
    const SI = 'si';
    const DEF = 'en';

    protected $languages = [
        'en' => [
            "title" => "English",
            "code" => "en",
            "international" => "en_EN",
        ],
        'si' => [
            "title" => "Slovenski",
            "code" => "slo",
            "international" => "sl_SI"
        ],
    ];

    protected $default = 1;
    protected $current = 1;

    protected $values;

    public function set($lang, $key, $val)
    {
        $this->values[$lang][$key] = $val;
    }

    public function setArray($lang, $arr)
    {
        foreach ($arr AS $key => $val) {
            $this->set($lang, $key, $val);
        }
    }

    public function get($key, $lang = NULL)
    {
        $lang = is_null($lang) ? $this->current : $lang;

        return isset($this->values[$lang][$key])
            ? $this->values[$lang][$key]
            : (isset($this->values[$this->default][$key])
                ? $this->values[$this->default][$key]
                : '__("' . $key . '")');
    }

    public function lang($lang = NULL)
    {
        if (is_null($lang))
            return $this->current;

        $this->current = $lang;
    }

    /*
    @param int|string $locale
    */
    public function setLocale($locale, $default = FALSE)
    {
        if (is_int($locale) && array_key_exists($locale, $this->languages)) {
            $default ? $this->current = $locale : $this->default = $locale;
        } else {
            throw new Exception("Cannot set locale.");
        }
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function getDefault()
    {
        return $this->default;
    }
}

?>