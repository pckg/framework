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
use Twig_Loader_String;
use Twig_SimpleFunction;

class Twig extends AbstractView implements ViewInterface
{

    protected $twig;
    protected $file = null;
    protected $data = [];

    function initTwig()
    {
        $this->twig = new Twig_Environment(new Twig_Loader_Chain([
                new Twig_Loader_Filesystem($this->getDirs()),
                new Twig_Loader_String(),
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

        /**
         * This is not needed anymore ...
         */
        $this->twig->addNodeVisitor(new TwigObjectizerNodeVisitor());
    }

    public function autoparse()
    {
        self::addDir(path('root'), Twig::PRIORITY_LAST);

        $this->initTwig();

        $this->twig = $this->twig->loadTemplate($this->file . ".twig");

        try {
            startMeasure('Rendering ' . $this->file);
            $render = $this->twig->render(array_merge(static::$staticData, $this->data));
            stopMeasure('Rendering ' . $this->file);

            if ($render == $this->file . '.twig') {
                d($this->getDirs());

                return "Cannot load file " . $render;
            }

            return $render;

        } catch (Twig_Error_Syntax $e) {
            return "<pre>Twig error:" . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . "</pre>";

        } catch (Exception $e) {
            return '<pre>' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . '</pre>';
        }

        throw new Exception('Cannot parse file \'' . $this->file . '\'');
    }

}