<?php

namespace Pckg\Framework\View;

use Carbon\Carbon;
use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\Router;
use Pckg\Framework\View\Event\RenderingView;
use Pckg\Htmlbuilder\Element\Select;
use Pckg\Manager\Locale;
use Throwable;
use Twig_Error_Syntax;
use Twig_Extension_Debug;
use Twig_Extension_StringLoader;
use Twig_Loader_Chain;
use Twig_Loader_Filesystem;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class Twig extends AbstractView implements ViewInterface
{

    protected $twig;

    protected $template = null;

    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
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

        $this->twig = new TwigEnv(
            new Twig_Loader_Chain(
                [
                    new Twig_Loader_Filesystem($dirs),
                    new \Twig_Loader_String(),
                ]
            ),
            [
                'debug' => dev(),
                //'cache' => path('cache') . 'view',
            ]
        );

        $this->twig->addExtension(new Twig_Extension_StringLoader());

        /**
         * This should be added to Dev environment Provider.
         */
        $this->twig->addExtension(new Twig_Extension_Debug());

        /**
         * This should be added to Framework/Inter Provider.
         */
        $this->twig->addFunction(
            new Twig_SimpleFunction(
                '__', function($key, $data = [], $lang = null) {
                return __($key, $data, $lang);
            },
                ['is_safe' => ['html']]
            )
        );
        /**
         * This should be added to Framework provider.
         */
        $this->twig->addFunction(
            new Twig_SimpleFunction(
                'config', function($text, $default = null) {
                return config($text, $default);
            }
            )
        );
        /**
         * This should be added to Framework provider.
         */
        $this->twig->addFunction(
            new Twig_SimpleFunction(
                'flash', function($key, $delete = true) {
                return context()->getOrCreate(Flash::class)->get($key, $delete);
            }
            )
        );
        /**
         * This should be added to Framework provider.
         */
        $this->twig->addFunction(
            new Twig_SimpleFunction(
                'url', function($url, $params = [], $absolute = false) {
                return context()->get(Router::class)->make($url, $params, $absolute);
            }
            )
        );
        $this->twig->addFunction(
            new Twig_SimpleFunction(
                'dev', function() {
                return dev();
            }
            )
        );
        $this->twig->addFunction(
            new Twig_SimpleFunction(
                'implicitDev', function() {
                return implicitDev();
            }
            )
        );
        $this->twig->addFunction(
            new Twig_SimpleFunction(
                'prod', function() {
                return prod();
            }
            )
        );
        /**
         * This should be added to Framework provider.
         */
        $this->twig->addFunction(
            new Twig_SimpleFunction(
                'media', function($file, $path = null, $relative = true, $base = null) {
                return media($file, $path, $relative, $base);
            }
            )
        );
        /**
         * This should be added to Framework provider.
         */
        $this->twig->addFunction(new Twig_SimpleFunction('cdn', function($file) {
            $host = config('storage.cdn.host');

            if (!$host) {
                return $file;
            }

            return '//' . $host . $file;
        }));
        /**
         * This should be added to Framework provider.
         */
        $this->twig->addFunction(
            new Twig_SimpleFunction(
                'relativePath', function($key) {
                return relativePath($key);
            }
            )
        );
        /**
         * This should be added to Framework provider.
         */
        $this->twig->addFunction(
            new Twig_SimpleFunction(
                'select', function($options, $attributes = [], $valueKey = null) {

                $select = new Select();
                $select->setAttributes($attributes ?? []);

                foreach ($options as $key => $option) {
                    $select->addOption($valueKey ? $option->id : $key, $valueKey ? $option->{$valueKey} : $option);
                }

                return $select;
            }
            )
        );
        /**
         * This should be added to Framework provider.
         */
        $this->twig->addFilter(
            new Twig_SimpleFilter(
                'price', function($price) {
                if (is_null($price)) {
                    $price = 0.0;
                }

                $localeManager = resolve(Locale::class);

                return number_format(
                           $price,
                           2,
                           $localeManager->getDecimalPoint(),
                           $localeManager->getThousandSeparator()
                       ) . ' €';
            }
            )
        );
        $this->twig->addFilter(
            new Twig_SimpleFilter(
                'roundPrice', function($price) {
                if (is_null($price)) {
                    $price = 0.0;
                }

                $localeManager = resolve(Locale::class);

                return trim(
                           (string)number_format(
                               $price,
                               2,
                               $localeManager->getDecimalPoint(),
                               $localeManager->getThousandSeparator()
                           ),
                           '0'
                       ) . ' €';
            }
            )
        );
        $this->twig->addFilter(
            new Twig_SimpleFilter(
                'datetime', function($date) {
                return (new Carbon($date))->format(resolve(Locale::class)->getDatetimeFormat());
            }
            )
        );
        $this->twig->addFilter(
            new Twig_SimpleFilter(
                'date', function($date) {
                return (new Carbon($date))->format(resolve(Locale::class)->getDateFormat());
            }
            )
        );

        $this->twig->getExtension('core')->setDateFormat(resolve(Locale::class)->getDateFormat(), '%d days');
    }

    public function getFullData()
    {
        return array_merge(static::$staticData, $this->data);
    }

    public function autoparse()
    {
        self::addDir(path('root'), Twig::PRIORITY_LAST);

        $this->initTwig($this->file);

        if ($this->file) {
            $this->twig = $this->twig->loadTemplate($this->file . ".twig");
        } else {
            $this->twig = $this->twig->createTemplate($this->template);
        }

        try {
            /**
             * Trigger rendering event so we can attach some handlers.
             */
            trigger(RenderingView::class, ['view' => $this->file, 'twig' => $this]);

            /**
             * Render template.
             */
            $render = measure(
                'Rendering ' . $this->file,
                function() {
                    return $this->twig->render($this->getFullData());
                }
            );

            if ($render == $this->file . '.twig') {
                if (prod()) {
                    return null;
                }

                return '<span style="color: black; font-weight: bold; background-color: red;">' . 'Cannot load file ' .
                       $this->file . '</span>';
            }

            return $render;
        } catch (Twig_Error_Syntax $e) {
            return "<pre>Twig error:" . exception($e) . "</pre>";
        } catch (Throwable $e) {
            return '<pre>' . exception($e) . '</pre>';
        }
    }

}