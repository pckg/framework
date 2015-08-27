<?php

namespace Pckg\View;

use Pckg\Router;
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

    function __construct($file, $data = [])
    {
        parent::__construct($file, $data);

        $this->setLoaders($this->file);
    }

    function initTwig()
    {
        $this->twig = new Twig_Environment(new Twig_Loader_Chain([
                new Twig_Loader_Filesystem($this->getDirs()),
                new Twig_Loader_String()]
        ),
            [
                'debug' => true
            ]
        );

        $this->twig->addExtension(new Twig_Extension_Debug());
        $this->twig->addExtension(new Twig_Extension_StringLoader());

        $this->twig->addFunction(new Twig_SimpleFunction('__', function ($text) {
            return \__($text);
        }));

        $this->twig->addFunction(new Twig_SimpleFunction('config', function ($text) {
            return context()->getBinded('Config');
        }));

        $this->twig->addFunction(new Twig_SimpleFunction('url', function ($url, $params = [], $absolute = false) {
            return context()->getBinded('Router')->make($url, $params, $absolute);
        }));

        $this->twig->addNodeVisitor(new TwigObjectizerNodeVisitor());
    }

    function autoparse()
    {
        self::addDir(path('root'));

        $this->initTwig();

        $this->twig = $this->twig->loadTemplate($this->file . ".twig");

        $config = config();
        if (isset($config['defaults']['twig']['classes']))
            foreach ($config['defaults']['twig']['classes'] AS $key => $class) {
                $this->data[$key] = new $class;
            }

        if (!isset($this->data['extends'])) {
            $this->data['extends'] = 'vendor/lfw/admin/src/Weblab/Admin/View/admin.twig'; // @T00D00
        }

        $this->data['debugBar'] = context()->exists('DebugBar')
            ? context()->find('DebugBar')->getJavascriptRenderer()
            : null;

        try {
            $render = $this->twig->render($this->data);

            if ($render == $this->file . '.twig') {
                return "Cannot load file " . $render;
            }

            return $render;

        } catch (\Twig_Error_Syntax $e) {
            return "<pre>Twig error:" . $e->getMessage() . "</pre>";

        } catch (\Exception $e) {
            return '<pre>' . $e->getMessage() . '</pre>';
        }

        return dev() ? "Cannot parse file '" . $this . "'" : NULL;
    }

}