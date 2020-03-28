<?php

namespace Pckg\Framework\View;

use Pckg\Auth\Form\Register;
use Pckg\Framework\Router;
use Pckg\Framework\View;
use Pckg\Framework\View\Event\RenderingView;
use Twig_Loader_Chain;
use Twig_Loader_Filesystem;

class Twig extends AbstractView implements ViewInterface
{

    /**
     * @var TwigEnv
     */
    protected $twig;

    protected $template = null;

    protected $debug = false;

    public function debug($debug = true)
    {
        $this->debug = $debug;

        return $this;
    }

    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

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
                $partDir = realpath($dir) . path('ds');

                $tempDir = $partDir . substr(str_replace('\\', path('ds'), $file), 0, strrpos($file, path('ds')));
                if (is_dir($tempDir)) {
                    $tempDirs[] = $tempDir;
                }
            }
            $dirs = array_unique($tempDirs);
        }

        if ($this->debug) {
            d($this->file, $dirs);
        }

        $this->twig = new TwigEnv(
            new Twig_Loader_Chain(
                [
                    new Twig_Loader_Filesystem($dirs),
                    new \Twig_Loader_String(),
                ]
            ),
            [
                'debug' => dev(),
                'cache' => path('cache') . 'view',
                'auto_reload' => true,
            ]
        );

        //(new View\Handler\RegisterTwigExtensions())->handle($this->twig);
        $this->twig->addFunction(new \Twig_SimpleFunction('__', function($key, $data = [], $lang = null) {
            return __($key, $data, $lang);
        }, ['is_safe' => ['html']]));

        $this->twig->addFilter(new \Twig_SimpleFilter('base64_encode', function($string) {
            return base64_encode($string);
        }));

        trigger(Twig::class . '.registerExtensions', $this->twig);
    }

    public function getFullData()
    {
        $data = array_merge(static::$staticData, $this->data);
        foreach ($data as &$val) {
            if (!is_only_callable($val)) {
                continue;
            }

            $val = $val();
        }
        return $data;
    }

    public function autoparse()
    {
        self::addDir(path('root'), Twig::PRIORITY_LAST);

        $this->initTwig($this->file);

        /**
         * Trigger loading event.
         */
        trigger(View::class . '.loading', ['view' => $this->file, 'twig' => $this]);

        if ($this->file) {
            $this->twig = $this->twig->loadTemplate($this->file . ".twig");
        } else {
            $this->twig = $this->twig->createTemplate($this->template);
        }

        /**
         * Render template.
         */
        $render = measure('Rendering ' . $this->file, function() {
            /**
             * Trigger rendering event so we can attach some handlers.
             */
            trigger(RenderingView::class, ['view' => $this->file, 'twig' => $this]);
            trigger(RenderingView::class . ':' . $this->file, ['twig' => $this]);

            return $this->twig->render($this->getFullData());
        });

        /**
         * Check if template wasn't loaded.
         */
        if ($render == $this->file . '.twig') {
            throw new \Exception('Cannot find file ' . $this->file . '.twig');
        }

        return $render;
    }

}