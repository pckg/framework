<?php

namespace Pckg\Framework\View;

use Exception;
use Pckg\Framework\Config;
use Pckg\Framework\Router;
use Twig_Environment;
use Twig_Error_Syntax;
use Twig_Extension_Debug;
use Twig_Extension_StringLoader;
use Twig_Loader_Chain;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

class Twig extends AbstractView implements ViewInterface
{

    protected $twig;
    protected $file = null;
    protected $data = [];

    function initTwig($file = null)
    {
        $dirs = $this->getDirs();

        /**
         * We need to duplicate every dir for proper relative includes ...
         *
         *
         */
        if ($file) {
            $tempDirs = $dirs;
            foreach ($dirs as $dir) {
                $tempDir = $dir . (substr($dir, -1) == path('ds') ? '' : path('ds')) . substr(str_replace('\\',
                        path('ds'), $file), 0, strrpos($file, '\\'));
                if (is_dir($tempDir)) {
                    $tempDirs[] = $tempDir;
                }
            }
            $dirs = array_unique($tempDirs);
        }

        $this->twig = new Twig_Environment(new Twig_Loader_Chain([
                new Twig_Loader_Filesystem($dirs),
            ]
        ),
            [
                'debug' => true,
                'cache' => path('cache') . 'view',
            ]
        );
        $this->twig->addExtension(new Twig_Extension_StringLoader());

        /**
         * This should be added to Dev environment Provider.
         */
        $this->twig->addExtension(new Twig_Extension_Debug());

        /**
         * This should be added to Framework Provider.
         */
        $this->twig->addFunction(new Twig_SimpleFunction('__', function ($text) {
            return \__($text);
        }));
        $this->twig->addFunction(new Twig_SimpleFunction('config', function ($text) {
            return context()->get(Config::class)->get($text);
        }));
        $this->twig->addFunction(new Twig_SimpleFunction('url', function ($url, $params = [], $absolute = false) {
            return context()->get(Router::class)->make($url, $params, $absolute);
        }));
    }

    public function getFullData()
    {
        return array_merge(static::$staticData, $this->data);
    }

    public function autoparse()
    {
        self::addDir(path('root'), Twig::PRIORITY_LAST);

        $this->initTwig($this->file);

        $this->twig = $this->twig->loadTemplate($this->file . ".twig");

        try {
            startMeasure('Rendering ' . $this->file);
            $render = $this->twig->render($this->getFullData());
            stopMeasure('Rendering ' . $this->file);

            if ($render == $this->file . '.twig') {
                d($this->getDirs());

                return "Cannot load file " . $this->file;
            }

            return $render;

        } catch (Twig_Error_Syntax $e) {
            return "<pre>Twig error:" . exception($e) . "</pre>";

        } catch (Exception $e) {
            return '<pre>' . exception($e) . '</pre>';

        }
    }

}