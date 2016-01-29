<?php

namespace Pckg\Framework\View;

use Pckg\Framework\Router;
use Twig_Environment;
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
                new Twig_Loader_String()]
        ),
            [
                'debug' => true,
                'cache' => path('cache') . 'view',
            ]
        );

        $this->twig->addExtension(new Twig_Extension_Debug());
        $this->twig->addExtension(new Twig_Extension_StringLoader());

        $this->twig->addFunction(new Twig_SimpleFunction('__', function ($text) {
            return \__($text);
        }));

        $this->twig->addFunction(new Twig_SimpleFunction('config', function ($text) {
            return context()->get('Config')->get($text);
        }));

        $this->twig->addFunction(new Twig_SimpleFunction('url', function ($url, $params = [], $absolute = false) {
            return context()->get('Router')->make($url, $params, $absolute);
        }));

        $this->twig->addNodeVisitor(new TwigObjectizerNodeVisitor());
    }

    function autoparse()
    {
        self::addDir(path('root'), Twig::PRIORITY_LAST);

        $this->initTwig();

        $this->twig = $this->twig->loadTemplate($this->file . ".twig");

        $config = config();
        if (isset($config['defaults']['twig']['classes']))
            foreach ($config['defaults']['twig']['classes'] AS $key => $class) {
                $this->data[$key] = new $class;
            }

        $this->data['debugBar'] = context()->exists('DebugBar')
            ? context()->find('DebugBar')->getJavascriptRenderer()
            : null;

        try {
            startMeasure('Rendering ' . $this->file);
            $render = $this->twig->render($this->data);
            stopMeasure('Rendering ' . $this->file);

            if ($render == $this->file . '.twig') {
                d($this->getDirs());
                return "Cannot load file " . $render;
            }

            return $render;

        } catch (\Twig_Error_Syntax $e) {
            return "<pre>Twig error:" . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . "</pre>";

        } catch (\Exception $e) {
            return '<pre>' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . '</pre>';
        }

        return dev() ? "Cannot parse file '" . $this . "'" : NULL;
    }

}