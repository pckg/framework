<?php

namespace Pckg\Framework;

use Exception;

class Lang
{

    const EN = 'en';

    const SI = 'si';

    const HR = 'hr';

    const DEF = 'en';

    protected $languages = [
        'en' => [
            "title"         => "English",
            "code"          => "en",
            "international" => "en_EN",
        ],
        'si' => [
            "title"         => "Slovenski",
            "code"          => "slo",
            "international" => "sl_SI",
        ],
        'hr' => [
            "title"         => "Hrvatski",
            "code"          => "hrv",
            "international" => "hr_HR",
        ],
    ];

    protected $default = 1;

    protected $current = 1;

    protected $values;

    public function setArray($lang, $arr)
    {
        foreach ($arr as $key => $val) {
            $this->set($lang, $key, $val);
        }
    }

    public function set($lang, $key, $val)
    {
        $this->values[$lang][$key] = $val;
    }

    public function get($key, $lang = null)
    {
        $lang = is_null($lang) ? $this->current : $lang;

        return isset($this->values[$lang][$key])
            ? $this->values[$lang][$key]
            : (isset($this->values[$this->default][$key])
                ? $this->values[$this->default][$key]
                : '__("' . $key . '")');
    }

    public function lang($lang = null)
    {
        if (is_null($lang)) {
            return $this->current;
        }

        $this->current = $lang;
    }

    /*
    @param int|string $locale
    */
    public function setLocale($locale, $default = false)
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
