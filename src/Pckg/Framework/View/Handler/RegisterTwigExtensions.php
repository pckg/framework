<?php namespace Pckg\Framework\View\Handler;

use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\Router;
use Pckg\Framework\View\TwigEnv;
use Pckg\Htmlbuilder\Element\Select;
use Pckg\Manager\Locale;
use Twig_Extension_Debug;
use Twig_Extension_StringLoader;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class RegisterTwigExtensions
{

    public function handle(TwigEnv $twig)
    {
        $twig->addExtension(new Twig_Extension_StringLoader());

        /**
         * This should be added to Dev environment Provider.
         */
        $twig->addExtension(new Twig_Extension_Debug());

        /**
         * This should be added to Framework/Inter Provider.
         */
        $twig->addFunction(new Twig_SimpleFunction('__', function($key, $data = [], $lang = null) {
            return __($key, $data, $lang);
        }, ['is_safe' => ['html']]));
        $twig->addFunction(new Twig_SimpleFunction('config', function($text, $default = null) {
            return config($text, $default);
        }));
        $twig->addFunction(new Twig_SimpleFunction('flash', function($key, $delete = true) {
            return context()->getOrCreate(Flash::class)->get($key, $delete);
        }));
        $twig->addFunction(new Twig_SimpleFunction('url', function($url, $params = [], $absolute = false) {
            return context()->get(Router::class)->make($url, $params, $absolute);
        }));
        $twig->addFunction(new Twig_SimpleFunction('dev', function() {
            return dev();
        }));
        $twig->addFunction(new Twig_SimpleFunction('implicitDev', function() {
            return implicitDev();
        }));
        $twig->addFunction(new Twig_SimpleFunction('prod', function() {
            return prod();
        }));
        /**
         * This should be added to Framework provider.
         */
        $twig->addFunction(new Twig_SimpleFunction('media',
            function($file, $path = null, $relative = true, $base = null) {
                return media($file, $path, $relative, $base);
            }));
        /**
         * This should be added to Framework provider.
         */
        $twig->addFunction(new Twig_SimpleFunction('cdn', function($file = null) {
            return cdn($file);
        }));

        $twig->addFunction(new Twig_SimpleFunction('imageCache', function($pic, $type, $arg) {
            return $pic ? '/cache/img/' . $type . '/' . $arg . $pic : null;
        }));

        /**
         * This should be added to Framework provider.
         */
        $twig->addFunction(new Twig_SimpleFunction('relativePath', function($key) {
            return relativePath($key);
        }));
        /**
         * This should be added to Framework provider.
         */
        $twig->addFunction(new Twig_SimpleFunction('select', function($options, $attributes = [], $valueKey = null) {

            $select = new Select();
            $select->setAttributes($attributes ?? []);

            foreach ($options as $key => $option) {
                $select->addOption($valueKey ? $option->id : $key, $valueKey ? $option->{$valueKey} : $option);
            }

            return $select;
        }));
        /**
         * This should be added to Framework provider.
         */
        $twig->addFilter(new Twig_SimpleFilter('price', function($price) {
            return price($price);
        }));
        $twig->addFilter(new Twig_SimpleFilter('roundPrice', function($price) {
            if (is_null($price)) {
                $price = 0.0;
            }

            $localeManager = resolve(Locale::class);

            return trim((string)number_format($price, 2, $localeManager->getDecimalPoint(),
                                              $localeManager->getThousandSeparator()), '0') . ' ' .
                config('pckg.payment.currencySign');
        }));
        $twig->addFilter(new Twig_SimpleFilter('datetime', function($date, $format = null) {
            return datetime($date, $format);
        }));
        $twig->addFilter(new Twig_SimpleFilter('date', function($date, $format = null) {
            return datef($date, $format);
        }));
        $twig->addFilter(new Twig_SimpleFilter('time', function($date, $format = null) {
            return timef($date, $format);
        }));

        $twig->getExtension('core')->setDateFormat(resolve(Locale::class)->getDateFormat(), '%d days');
    }

}