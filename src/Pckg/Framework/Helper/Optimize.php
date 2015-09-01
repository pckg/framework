<?php

namespace Pckg\Framework\Helper;

class Optimize
{
    private static $files = [];
    public static $types = [
        "css" => [
            "header" => "text/css",
            "ext" => "css",
            "html" => '<link rel="stylesheet" type="text/css" href="##LINK##" />'
        ],
        "less" => [
          "header" => "text/css",
          "ext" => "css",
          "html" => '<link rel="stylesheet/less" type="text/css" href="##LINK##" />'
        ],
        "js" => [
            "header" => "text/javascript",
            "ext" => "js",
            "html" => '<script type="text/javascript" src="##LINK##"></script>'
        ]
    ];
    private static $content = [];

    public static function addAssets($files, $section = 'main')
    {
        $backtrace = debug_backtrace();

        if (!is_array($files))
            $files = (array)$files;

        foreach ($files AS $file) {
            $publicPartPrefix = strpos($backtrace[0]["file"], path('root') . 'vendor/') === 0
                ? 6 // vendor/a/b/src/A/B/public
                : 2; // A/B/public
            $path = implode("/", array_slice(explode("/", str_replace(["\\", path('root')], ["/", ''], $backtrace[0]["file"])), 0, $publicPartPrefix)) . "/public/" . $file;

            if (is_file(path('root') . $path)) {
                self::addFile(substr(strrchr($file, '.'), 1), $path, $section);
            } else {
                die(path('root') . $path); // @T00D00
                throw new \Exception(path('root') . $path);
            }
        }
    }

    /*
     * Metoda za dodajanje datotek.
     *
     * $type - eden izmed zgoraj navedenih tipov datotek (prvi index)
     * $files - ena (string)  ali več datotek (enodimenzionalen array)
     * $section - privzeto main, lahko se doda dodatna sekcija. Za vsako sekcijo se ustvari svoja cache datoteka (main za datoteke, ki se vedno postavijo na strani in poljubno število drugih za v večini nepomembne datoteke)
     */
    public static function addFile($type, $files, $section = "main")
    {
        // type must be set
        if (!isset(self::$types[$type]) || empty($files))
            return FALSE;

        // if files are not in array, we make array
        if (!is_array($files))
            $files = [$files];

        foreach ($files AS $file) {
            if (!isset(self::$files[$type][$section]) || !in_array($file, self::$files[$type][$section]))
                self::$files[$type][$section][] = $file;
        }
    }

    /*
     * Generira končni HTML.
     */
    public static function getHTML($types = NULL)
    {
        $html = [];
        $types = is_null($types)
            ? ["css", "js"]
            : is_array($types)
                ? $types
                : [$types];
        foreach (self::$types AS $type => $conf) {
            if (!is_null($types) && !in_array($type, $types)) {
                continue;
            }
            $dir = path('cache') . $type . "/";

            // create cache dir
            if (!is_dir($dir)) {
                mkdir($dir, 777, TRUE);
            }

            // foreach filetype as section
            if (isset(self::$files[$type]))
                foreach (self::$files[$type] AS $section) {
                    $lastchange = self::getChangeTime($section);

                    $hash = sha1(implode($section) . $lastchange);

                    $filename = $hash . "." . self::$types[$type]['ext'];

                    if (TRUE || dev()) {
                        foreach ($section AS $key => $file) {
                            $html[] = str_replace("##LINK##", (substr($file, 0, 4) == 'http' ? '' : "/") . $file, self::$types[$type]['html']);
                        }
                    } else {
                        // if file doesn't exist, we create it
                        if (!is_file($dir . $filename) || self::isOldCache($section, $filename)) {
                            $fileContent = NULL;

                            // foreach section as file
                            foreach ($section AS $key => $file) {
                                $explode = explode(".", $file);
                                if (end($explode) == $conf['ext']
                                    && ((is_file(path('www') . $file) && $content = file_get_contents(path('www') . $file)) || (is_file(path('app') . $file) && $content = file_get_contents(path('app') . $file)))
                                ) {
                                    $fileContent .= $content;
                                }
                            }

                            // save file
                            if (is_writable($dir))
                                file_put_contents($dir . $filename, ($type !== "css" ? $fileContent : self::compress($fileContent)));
                        }

                        $html[] = str_replace("##LINK##", str_replace(path('cache'), '/cache/', $dir) . $filename, self::$types[$type]['html']);
                    }
                }
        }

        return $html
            ? '<!-- optimize -->' . implode("\n", $html) . '<!-- /optimize -->'
            : null;
    }

    private static function compress($buffer)
    {
        /* remove comments */
        $buffer = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $buffer);
        /* remove tabs, spaces, newlines, etc. */
        $buffer = str_replace(["\r\n", "\r", "\t", "\n", '  ', '    ', '     '], '', $buffer);
        /* remove other spaces before/after ) */
        $buffer = preg_replace(['(( )+\))', '(\)( )+)'], ')', $buffer);
        return $buffer;
    }

    public static function getChangeTime($section)
    {
        $time = 0;

        if (dev())
            return $time;

        foreach ($section AS $key => $file) {
            if (is_file(path('www') . $file)) {
                if (filemtime(path('www') . $file) > $time) {
                    $time = filemtime(path('www') . $file);
                }
            }
        }

        return $time;
    }

    /*
     * Preveri starost cachea
     *
     * $section - ime sekcije
     * $filename - ime datoteke
     */
    public static function isOldCache($section, $filename)
    {
        return is_file($filename) ? self::getChangeTime($section) > filemtime($filename) : true;
    }

    /*
     * Vrne HTML
     *
     */
    public function __toString()
    {
        return (string)$this->getHTML();
    }
}

?>