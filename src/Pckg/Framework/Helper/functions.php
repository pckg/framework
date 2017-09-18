<?php

use Carbon\Carbon;
use DebugBar\DebugBar;
use Pckg\Auth\Service\Auth;
use Pckg\Collection;
use Pckg\Concept\ChainOfResponsibility;
use Pckg\Concept\Context;
use Pckg\Concept\Event\AbstractEvent;
use Pckg\Concept\Event\Dispatcher;
use Pckg\Framework\Application;
use Pckg\Framework\Config;
use Pckg\Framework\Environment;
use Pckg\Framework\Request;
use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\Request\Data\Session;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Pckg\Framework\View\Twig;
use Pckg\Manager\Asset;
use Pckg\Manager\Cache;
use Pckg\Manager\Gtm;
use Pckg\Manager\Locale;
use Pckg\Manager\Meta;
use Pckg\Manager\Seo;
use Pckg\Manager\Vue;
use Pckg\Queue\Service\Queue;
use Pckg\Translator\Service\Translator;

/* context */

/**
 * @return Context
 * @throws Exception
 */
if (!function_exists('context')) {
    function context($key = null, $val = null)
    {
        $context = Context::getInstance();

        if ($val) {
            return $context->bind($key, $val);
        } else if ($key) {
            return $context->get($key);
        }

        return $context;
    }
}

/**
 * @return Environment
 */
if (!function_exists('env')) {
    function env()
    {
        return context()->get(Environment::class);
    }
}

if (!function_exists('object_implements')) {
    function object_implements($object, $interface)
    {
        return (is_object($object) || is_string($object)) && in_array($interface, class_implements($object));
    }
}

/**
 * @return Application
 */
if (!function_exists('app')) {
    function app()
    {
        return context()->get(Application::class);
    }
}

if (!function_exists('getDotted')) {
    function getDotted($data, $keys, $i = 0)
    {
        if (!isset($keys[$i])) {
            return $data;
        } else if (isset($data[$keys[$i]])) {
            return getDotted($data[$keys[$i]], $keys, $i + 1);
        }

        return null;
    }
}

if (!function_exists('hasDotted')) {
    function hasDotted($data, $keys, $i = 0)
    {
        if (!isset($keys[$i])) {
            return true;
        } else if (array_key_exists($keys[$i], $data)) {
            return hasDotted($data[$keys[$i]], $keys, $i + 1);
        }

        return false;
    }
}

if (!function_exists('request')) {
    /**
     * @return Request
     */
    function request()
    {
        return context()->get(Request::class);
    }
}

/**
 * @return Request|mixed
 */
if (!function_exists('post')) {
    function post($key = null, $default = [])
    {
        return request()->post($key, $default);
    }
}

/**
 * @return Request|mixed
 */
if (!function_exists('get')) {
    function get($key = null, $default = [])
    {
        return request()->get($key, $default);
    }
}

/**
 * @return Request|mixed
 */
if (!function_exists('server')) {
    function server($key = null, $default = [])
    {
        return request()->server($key, $default);
    }
}

/**
 * @return Request
 */
if (!function_exists('files')) {
    function files($key = null, $default = [])
    {
        return request()->files($key, $default);
    }
}

if (!function_exists('auth')) {
    /**
     * @return Auth
     */
    function auth($provider = null)
    {
        $auth = context()->getOrCreate(Auth::class);

        if ($provider) {
            $auth->useProvider($provider);
        }

        return $auth;
    }
}

if (!function_exists('response')) {
    /**
     * @return Response
     */
    function response()
    {
        return context()->get(Response::class);
    }
}

if (!function_exists('redirect')) {
    function redirect($url = null)
    {
        return response()->redirect($url);
    }
}

if (!function_exists('internal')) {
    function internal($url = null)
    {
        return response()->internal($url);
    }
}

if (!function_exists('entity')) {
    /**
     * @param null $entity
     *
     * @return \Pckg\Database\Entity
     */
    function entity($entity = null)
    {
        return context()->getEntity($entity);
    }
}

if (!function_exists('form')) {
    /**
     * @param null $form
     *
     * @return \Htmlbuilder\Element\Form
     */
    function form($form = null)
    {
        return context()->getForm($form);
    }
}

if (!function_exists('factory')) {
    /**
     * @param $factory
     *
     * @return \Pckg\Concept\Factory
     */
    function factory($factory)
    {
        return context()->getFactory($factory);
    }
}

/* event */

if (!function_exists('dispatcher')) {
    /**
     *
     * @return Dispatcher
     * */
    function dispatcher()
    {
        return context()->get(Dispatcher::class);
    }
}

if (!function_exists('trigger')) {
    /**
     * @param       $event
     * @param null  $method
     * @param array $args
     *
     * @return mixed|null|object
     */
    function trigger($event, $args = [], $method = null)
    {
        return dispatcher()->trigger($event, $args, $method);
    }
}

if (!function_exists('schedule')) {
    /**
     * @param \Event $event
     * @param        $strtotime
     */
    function schedule(AbstractEvent $event, $strtotime)
    {
        // Event::schedule($event, $strtotime);
    }
}

if (!function_exists('listen')) {
    /**
     *
     * @return Pckg\Concept\Event\Dispatcher
     * */
    function listen($event, $eventHandler)
    {
        return dispatcher()->listen($event, $eventHandler);
    }
}

if (!function_exists('listenOnce')) {
    function listenOnce($event, $eventHandler)
    {
        if (dispatcher()->hasListener($event, $eventHandler)) {
            return;
        }

        return dispatcher()->listen($event, $eventHandler);
    }
}

if (!function_exists('registerEvent')) {
    function registerEvent(AbstractEvent $event)
    {
        return dispatcher()->registerEvent($event);
    }
}

if (!function_exists('triggerEvent')) {
    function triggerEvent($event, $args = [])
    {
        return dispatcher()->trigger($event, $args, 'handle');
    }
}

/* router */

if (!function_exists('router')) {
    /**
     * @return \Pckg\Framework\Router
     */
    function router()
    {
        return context()->get(Router::class);
    }
}

if (!function_exists('url')) {
    /**
     * @param       $url
     * @param array $params
     *
     * @return string
     */
    function url($url, $params = [], $absolute = false, $envPrefix = true)
    {
        try {
            $url = router()->make($url, $params, $absolute, $envPrefix);

            return $url;
        } catch (Throwable $e) {
            if (prod()) {
                return null;
            }

            return exception($e);
        }
    }
}

if (!function_exists('email')) {
    function email($template, $receiver, $data = [])
    {
        if (!$template) {
            throw new Exception("Mail template is missing!");
        }

        return
            queue()->create(
                'mail:send',
                [
                    'user'     => $receiver,
                    'template' => $template,
                    'data'     => $data,
                ]
            );
    }
}

if (!function_exists('resolve')) {
    function resolve($class)
    {
        return context()->getOrCreate($class);
    }
}

if (!function_exists('queue')) {
    /**
     * @return Queue
     */
    function queue()
    {
        return context()->getOrCreate(Queue::class);
    }
}

if (!function_exists('chain')) {
    /**
     * @param       $chains
     * @param null  $method
     * @param array $args
     * @param null  $firstChain
     *
     * @return mixed|null|object
     * @throws Exception
     */
    function chain($chains, $method = 'execute', array $args = [], $firstChain = null)
    {
        return (new ChainOfResponsibility($chains, $method, $args, $firstChain))->runChains();
    }
}

/* session */

if (!function_exists('session')) {
    /**
     * @return mixed
     */
    function session()
    {
        return context()->getOrCreate(Session::class);
    }
}

if (!function_exists('flash')) {
    /**
     * @param      $key
     * @param null $val
     *
     * @return mixed|Flash
     */
    function flash($key, $val)
    {
        return context()->getOrCreate(Flash::class)->set($key, $val);
    }
}

/* config */

if (!function_exists('config')) {
    /**
     * @param $text
     *
     * @return mixed|Config|array|callable
     */
    function config($key = null, $default = null)
    {
        $config = context()->get(Config::class);

        if ($key) {
            return $config->get($key) ?? $default;
        }

        return $config;
    }
}

if (!function_exists('first')) {
    function first(...$args)
    {
        foreach ($args as $arg) {
            if ($arg) {
                return $arg;
            }
        }

        return null;
    }
}

if (!function_exists('path')) {
    /**
     * @param      $key
     * @param null $val
     *
     * @return array|null
     */
    function path($key = null, $val = null)
    {
        if ($val) {
            context()->getOrCreate(Config::class)->set('path.' . $key, $val);
        }

        return $val = config('path.' . $key);
    }
}

if (!function_exists('relativePath')) {
    /**
     * @param      $key
     * @param null $val
     *
     * @return array|null
     */
    function relativePath($key = null)
    {
        return str_replace(path('root'), '/', config('path.' . $key));
    }
}

/* quick helpers */

if (!function_exists('__i18n')) {
    function __i18n($key, $data = [], $lang = null)
    {
        try {
            $translator = context()->getOrCreate(
                Translator::class,
                [],
                function(Translator $translator) {
                    $translator->boot();
                }
            );

            $translation = trim($translator->get($key, $lang));

            return $data
                ? (new Twig(null, $data))->setTemplate($translation)->autoparse()
                : $translation;
        } catch (Throwable $e) {
            if (prod()) {
                return $key;
            }

            throw $e;
        }
    }
}

if (!function_exists('__')) {
    function __($key, $data = [], $lang = null)
    {
        return __i18n($key, $data, $lang);
    }
}

if (!function_exists('toCamel')) {
    /**
     * @param $text
     *
     * @return string
     */
    function toCamel($text)
    {
        $text = str_split($text, 1);

        foreach ($text AS $index => $char) {
            if (($char == "_" && isset($text[$index + 1]))
                || ($char == "\\" && isset($text[$index + 1]))
            ) {
                $text[$index + 1] = mb_strtoupper($text[$index + 1]);
            }
        }

        return ucfirst(str_replace("_", "", implode($text)));
    }
}

if (!function_exists('view')) {
    /**
     * @param       $view
     * @param array $data
     *
     * @return Twig
     */
    function view($view = null, $data = [], $assets = [])
    {
        $view = new Twig($view, $data);
        if ($parent = realpath(
            dirname(debug_backtrace()[0]['file']) . path('ds') . '..' . path('ds') . 'View' . path('ds')
        )
        ) {
            if (is_dir($parent)) {
                $view->addDir($parent, Twig::PRIORITY_LAST);
            }
            $calculatedParent = realpath(
                dirname(debug_backtrace()[0]['file']) . path('ds') . '..' . path('ds') . '..' . path(
                    'ds'
                ) . '..' . path(
                    'ds'
                )
            );
            if (is_dir($calculatedParent)) {
                $view->addDir($calculatedParent, Twig::PRIORITY_LAST);
            }
        }

        if ($assets) {
            assets($assets);
        }

        return $view;
    }
}

if (!function_exists('assetManager')) {
    /**
     * @return Asset
     */
    function assetManager()
    {
        return context()->getOrCreate(Asset::class);
    }
}

if (!function_exists('vueManager')) {
    /**
     * @return Vue
     */
    function vueManager()
    {
        return context()->getOrCreate(Vue::class);
    }
}

if (!function_exists('seoManager')) {
    /**
     * @return Seo
     */
    function seoManager()
    {
        return context()->getOrCreate(Seo::class);
    }
}

if (!function_exists('localeManager')) {
    /**
     * @return Locale
     */
    function localeManager()
    {
        return context()->getOrCreate(Locale::class);
    }
}

if (!function_exists('metaManager')) {
    /**
     * @return Meta
     */
    function metaManager()
    {
        return context()->getOrCreate(Meta::class);
    }
}

if (!function_exists('gtmManager')) {
    /**
     * @return Gtm
     */
    function gtmManager()
    {
        return context()->getOrCreate(Gtm::class);
    }
}

if (!function_exists('assets')) {
    function assets($assets)
    {
        assetManager()->addAssets($assets);
    }
}

if (!function_exists('autoloader')) {
    /**
     * @return mixed
     */
    function autoloader()
    {
        return require BASE_PATH . "vendor/autoload.php";
    }
}

if (!function_exists('isConsole')) {
    function isConsole()
    {
        return !isset($_SERVER['HTTP_HOST']);
    }
}

if (!function_exists('isHttp')) {
    function isHttp()
    {
        return isset($_SERVER['HTTP_HOST']);
    }
}

if (!function_exists('dd')) {
    function dd(...$mixed)
    {
        foreach ($mixed as $m) {
            d($m);
        }
        if (context() && context()->exists(Session::class)) {
            context()->get(Session::class)->__destruct();
        }
        die();
    }
}

if (!function_exists('d')) {
    function d(...$mixed)
    {
        foreach ($mixed as $m) {
            echo '<pre>';
            if (is_string($m)) {
                echo $m;
            } else {
                var_dump($m);
            }
            echo '</pre>';
            echo "<br />\n";
        }

        return true;
    }
}

if (!function_exists('db')) {
    function db($depth = 3, $start = 0)
    {
        $db = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        for ($i = $start; $i <= $depth + $start && isset($db[$i]); $i++) {
            d($db[$i]);
        }
    }
}

if (!function_exists('dev')) {
    function dev()
    {
        return env()->isDev();
    }
}

if (!function_exists('prod')) {
    function prod()
    {
        return env()->isPro();
    }
}

if (!function_exists('implicitDev')) {
    function implicitDev()
    {
        return (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], config('pckg.framework.dev', [])));
    }
}

if (!function_exists('win')) {
    function win()
    {
        return env()->isWin();
    }
}

if (!function_exists('unix')) {
    function unix()
    {
        return env()->isUnix();
    }
}

if (!function_exists('debugBar')) {
    /**
     * @return DebugBar
     */
    function debugBar()
    {
        return context()->exists(DebugBar::class)
            ? context()->get(DebugBar::class)
            : null;
    }
}

if (!function_exists('message')) {
    function message($message, $collector = 'messages')
    {
        if ($debugBar = debugBar()) {
            if (!$debugBar->hasCollector($collector)) {
                $debugBar->addCollector(new \DebugBar\DataCollector\MessagesCollector($collector));
            }

            $debugBar->getCollector($collector)->addMessage($message);
        }
    }
}

if (!function_exists('measure')) {
    function measure($message, callable $callback)
    {
        startMeasure($message);
        $result = $callback();
        stopMeasure($message);

        return $result;
    }
}

if (!function_exists('startMeasure')) {
    function startMeasure($name)
    {
        if ($debugBar = debugBar()) {
            try {
                $debugBar['time']->startMeasure($name);
            } catch (Throwable $e) {
                // fail silently
            }
        }
    }
}

if (!function_exists('stopMeasure')) {
    function stopMeasure($name)
    {
        if ($debugBar = debugBar()) {
            try {
                $debugBar['time']->stopMeasure($name);
            } catch (Throwable $e) {
                // fail silently
            }
        }
    }
}

if (!function_exists('collect')) {
    function collect($data, $of = Collection::class)
    {
        return new $of($data);
    }
}

if (!function_exists('array_merge_array')) {
    function array_merge_array($merge, $to)
    {
        foreach ($to as $key => &$val) {
            $val = array_merge($merge, $val);
        }

        return $to;
    }
}

if (!function_exists('merge_arrays')) {
    function merge_arrays($to, $merge)
    {
        foreach ($merge as $key => $val) {
            /**
             * Value is set first time.
             */
            if (!array_key_exists($key, $to)) {
                $to[$key] = $val;
                continue;
            }

            /**
             * Value is final.
             */
            if (!is_array($val)) {
                $to[$key] = $val;
                continue;
            }

            /**
             * Value is list of items.
             */
            if (isArrayList($val)) {
                $to[$key] = $val;
                continue;
            }

            /**
             * Value is keyed array.
             */
            if (is_array($to[$key])) {
                $to[$key] = merge_arrays($to[$key], $val);
            }
        }

        return $to;
    }
}

if (!function_exists('str_lreplace')) {
    function str_lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);

        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }
}

if (!function_exists('exception')) {
    /**
     * @param Exception $e
     *
     * @return string
     */
    function exception(Throwable $e)
    {
        return $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine();
    }
}

if (!function_exists('img')) {
    function img($name, $dir = null, $relative = true, $base = null)
    {
        if (!$name) {
            return null;
        }

        if (!$base) {
            $base = path('app_uploads');
        }

        if ($dir) {
            $base .= $dir . '/';
        }

        return $relative
            ? str_replace(path('root'), path('ds'), $base) . $name
            : $base . $name;
    }
}

if (!function_exists('media')) {
    function media($name, $dir = null, $relative = true, $base = null)
    {
        return img($name, $dir, $relative, $base);
    }
}

if (!function_exists('runInLocale')) {
    function runInLocale($call, $locale)
    {
        $prevLocale = localeManager()->getCurrent();
        if ($locale != $prevLocale) {
            localeManager()->setCurrent($locale);
            $response = $call();
            localeManager()->setCurrent($prevLocale);
        } else {
            $response = $call();
        }

        return $response;
    }
}

if (!function_exists('isArrayList')) {
    function isArrayList($array)
    {
        return array_keys($array) === range(0, count($array) - 1);
    }
}

if (!function_exists('sluggify')) {
    function sluggify($str, $separator = '-')
    {
        # special accents
        $a = [
            'À',
            'Á',
            'Â',
            'Ã',
            'Ä',
            'Å',
            'Æ',
            'Ç',
            'È',
            'É',
            'Ê',
            'Ë',
            'Ì',
            'Í',
            'Î',
            'Ï',
            'Ð',
            'Ñ',
            'Ò',
            'Ó',
            'Ô',
            'Õ',
            'Ö',
            'Ø',
            'Ù',
            'Ú',
            'Û',
            'Ü',
            'Ý',
            'ß',
            'à',
            'á',
            'â',
            'ã',
            'ä',
            'å',
            'æ',
            'ç',
            'è',
            'é',
            'ê',
            'ë',
            'ì',
            'í',
            'î',
            'ï',
            'ñ',
            'ò',
            'ó',
            'ô',
            'õ',
            'ö',
            'ø',
            'ù',
            'ú',
            'û',
            'ü',
            'ý',
            'ÿ',
            'A',
            'a',
            'A',
            'a',
            'A',
            'a',
            'C',
            'c',
            'C',
            'c',
            'C',
            'c',
            'C',
            'c',
            'D',
            'd',
            'Ð',
            'd',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'G',
            'g',
            'G',
            'g',
            'G',
            'g',
            'G',
            'g',
            'H',
            'h',
            'H',
            'h',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            '?',
            '?',
            'J',
            'j',
            'K',
            'k',
            'L',
            'l',
            'L',
            'l',
            'L',
            'l',
            '?',
            '?',
            'L',
            'l',
            'N',
            'n',
            'N',
            'n',
            'N',
            'n',
            '?',
            'O',
            'o',
            'O',
            'o',
            'O',
            'o',
            'Œ',
            'œ',
            'R',
            'r',
            'R',
            'r',
            'R',
            'r',
            'S',
            's',
            'S',
            's',
            'S',
            's',
            'Š',
            'š',
            'T',
            't',
            'T',
            't',
            'T',
            't',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'W',
            'w',
            'Y',
            'y',
            'Ÿ',
            'Z',
            'z',
            'Z',
            'z',
            'Ž',
            'ž',
            '?',
            'ƒ',
            'O',
            'o',
            'U',
            'u',
            'A',
            'a',
            'I',
            'i',
            'O',
            'o',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            '?',
            '?',
            '?',
            '?',
            '?',
            '?',
        ];
        $b = [
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'AE',
            'C',
            'E',
            'E',
            'E',
            'E',
            'I',
            'I',
            'I',
            'I',
            'D',
            'N',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'U',
            'U',
            'U',
            'U',
            'Y',
            's',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'ae',
            'c',
            'e',
            'e',
            'e',
            'e',
            'i',
            'i',
            'i',
            'i',
            'n',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'u',
            'u',
            'u',
            'u',
            'y',
            'y',
            'A',
            'a',
            'A',
            'a',
            'A',
            'a',
            'C',
            'c',
            'C',
            'c',
            'C',
            'c',
            'C',
            'c',
            'D',
            'd',
            'D',
            'd',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'G',
            'g',
            'G',
            'g',
            'G',
            'g',
            'G',
            'g',
            'H',
            'h',
            'H',
            'h',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'IJ',
            'ij',
            'J',
            'j',
            'K',
            'k',
            'L',
            'l',
            'L',
            'l',
            'L',
            'l',
            'L',
            'l',
            'l',
            'l',
            'N',
            'n',
            'N',
            'n',
            'N',
            'n',
            'n',
            'O',
            'o',
            'O',
            'o',
            'O',
            'o',
            'OE',
            'oe',
            'R',
            'r',
            'R',
            'r',
            'R',
            'r',
            'S',
            's',
            'S',
            's',
            'S',
            's',
            'S',
            's',
            'T',
            't',
            'T',
            't',
            'T',
            't',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'W',
            'w',
            'Y',
            'y',
            'Y',
            'Z',
            'z',
            'Z',
            'z',
            'Z',
            'z',
            's',
            'f',
            'O',
            'o',
            'U',
            'u',
            'A',
            'a',
            'I',
            'i',
            'O',
            'o',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'A',
            'a',
            'AE',
            'ae',
            'O',
            'o',
        ];

        return strtolower(
            preg_replace(['/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'], ['', $separator, ''], str_replace($a, $b, $str))
        );
    }
}

if (!function_exists('get_date_diff')) {
    function get_date_diff($time1, $time2, $precision = 2)
    {
        // If not numeric then convert timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }
        // If time1 > time2 then swap the 2 values
        if ($time1 > $time2) {
            list($time1, $time2) = [$time2, $time1];
        }
        // Set up intervals and diffs arrays
        $intervals = ['year', 'month', 'day', 'hour', 'minute', 'second'];
        $diffs = [];
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }
            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }
        $count = 0;
        $times = [];
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval if value is bigger than 0
            if ($value > 0) {
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }

        // Return string with times
        return implode(", ", $times);
    }
}

if (!function_exists('br2nl')) {
    function br2nl($string)
    {
        $string = str_replace(['<br />', '<br/>', '<br>'], "\n", $string);
        $string = str_replace('"', '\"', $string);

        return '"' . $string . '"';
    }
}

if (!function_exists('array_union')) {
    function array_union($one, $two)
    {
        return array_merge(
            array_intersect($one, $two),
            array_diff($one, $two),
            array_diff($two, $one)
        );
    }
}

if (!function_exists('transform')) {
    function transform($collection, $rules)
    {
        return collect($collection)->map($rules)->all();
    }
}

if (!function_exists('cache')) {
    function cache($key, $value, $type = 'request', $time = 0)
    {
        $cache = context()->getOrCreate(Cache::class);

        return $cache->cache($key, $value, $type, $time);
    }
}

if (!function_exists('between')) {
    function between($value, $min, $max)
    {
        $value = (int)$value;
        if ($value < $min) {
            $value = $min;
        } else if ($value > $max) {
            $value = $max;
        }

        return $value;
    }
}

if (!function_exists('route')) {
    function route($route, $view, $controller = null)
    {
        return new Pckg\Framework\Router\Route\Route($route, $view, $controller);
    }
}

if (!function_exists('routeGroup')) {
    function routeGroup($data = [], $routes)
    {
        $routeGroup = new Pckg\Framework\Router\Route\Group($data);

        if ($routes) {
            $routeGroup->routes($routes);
        }

        return $routeGroup;
    }
}

if (!function_exists('price')) {
    function price($price)
    {
        if (is_null($price)) {
            $price = 0.0;
        }

        $localeManager = resolve(Locale::class);

        return number_format(
                   $price,
                   2,
                   $localeManager->getDecimalPoint(),
                   $localeManager->getThousandSeparator()
               ) . ' ' . config('pckg.payment.currencySign');
    }
}

if (!function_exists('is_only_callable')) {
    function is_only_callable($input)
    {
        if (is_string($input)) {
            return false;
        }

        return is_callable($input);
    }
}

if (!function_exists('is_associative_array')) {
    function is_associative_array($array)
    {
        return is_array($array) && (!$array || range(0, count($array) - 1) == array_keys($array));
    }
}

if (!function_exists('strbetween')) {
    function strbetween($text, $from, $to)
    {
        $start = strpos($text, $from) + strlen($from);
        $end = strpos($text, $to, $start);

        return substr($text, $start, $end - $start);
    }
}

if (!function_exists('cdn')) {
    function cdn($file)
    {
        $host = config('storage.cdn.host');

        if (!$host) {
            return $file;
        }

        return '//' . $host . $file;
    }
}

if (!function_exists('only')) {
    function only($array, $keys)
    {
        $final = [];

        foreach ($keys as $key) {
            $final[$key] = $array[$key] ?? null;
        }

        return $final;
    }
}

if (!function_exists('datetime')) {
    function datetime($date, $format = null) {
        if (!$format) {
            $format = resolve(Locale::class)->getDatetimeFormat();
        }

        return (new Carbon($date))->format($format);
    }
}

if (!function_exists('datef')) {
    function datef($date, $format = null) {
        if (!$format) {
            $format = resolve(Locale::class)->getDateFormat();
        }

        return (new Carbon($date))->format($format);
    }
}

if (!function_exists('timef')) {
    function timef($date, $format = null) {
        if (!$format) {
            $format = resolve(Locale::class)->getTimeFormat();
        }

        return (new Carbon($date))->format($format);
    }
}
