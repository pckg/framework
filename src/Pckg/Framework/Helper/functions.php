<?php

use Carbon\Carbon;
use Pckg\Auth\Service\Auth;
use Pckg\Concept\ChainOfResponsibility;
use Pckg\Concept\Event\AbstractEvent;
use Pckg\Concept\Reflect;
use Pckg\Framework\Application;
use Pckg\Framework\Config;
use Pckg\Framework\Environment;
use Pckg\Framework\Request;
use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\Request\Data\Session;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Pckg\Framework\View\Twig;
use Pckg\Generic\Service\Generic;
use Pckg\Mail\Service\Mail\Handler\Queue as QueueMailHandler;
use Pckg\Manager\Asset;
use Pckg\Manager\Cache;
use Pckg\Manager\Gtm;
use Pckg\Manager\Locale;
use Pckg\Manager\Meta;
use Pckg\Manager\Seo;
use Pckg\Manager\Vue;
use Pckg\Queue\Service\Queue;
use Pckg\Translator\Service\Translator;

/**
 * @return Environment
 */
if (!function_exists('env')) {
    function env()
    {
        return context()->get(Environment::class);
    }
}

/**
 * @return mixed|string|array
 */
if (!function_exists('dotenv')) {
    function dotenv($key, $default = null)
    {
        $dotenv = context()->getOrCreate(\josegonzalez\Dotenv\Loader::class, [], null, function() {
            $file = BASE_PATH . '.env';
            if (!file_exists($file)) {
                return;
            }
            $dotenv = new \josegonzalez\Dotenv\Loader($file);

            $dotenv->parse();

            return $dotenv;
        });

        if (!$dotenv) {
            return $default;
        }

        $var = $dotenv->toArray();

        return $var[$key] ?? $default;
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
        } elseif (isset($data[$keys[$i]])) {
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
        } elseif (!$data) {
            return false;
        } elseif (is_scalar($data) && $data != $keys[$i]) {
            return false;
        } elseif (isset($data[$keys[$i]]) || array_key_exists($keys[$i], $data)) {
            return hasDotted($data[$keys[$i]], $keys, $i + 1);
        }

        return false;
    }
}

if (!function_exists('retry')) {
    function retry(callable $task, int $times = null, callable $onError = null, $interval = null)
    {
        $retry = new \Pckg\Framework\Helper\Retry($task);

        if ($times) {
            $retry->times($times);
        }

        if ($interval) {
            $retry->interval($interval);
        }

        if ($onError) {
            $retry->onError($onError);
        }

        return $retry->make();
    }
}

if (!function_exists('request')) {
    /**
     * @return Request
     */
    function request()
    {
        return context()->getOrCreate(Request::class);
    }
}

/**
 * @return Post|mixed|string|null|array
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
 * @return Request|mixed
 */
if (!function_exists('cookie')) {
    function cookie($key = null, $default = [])
    {
        return request()->cookie($key, $default);
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

/**
 * @return mixed
 */
if (!function_exists('required')) {
    function required($value, $type = null, $key = null)
    {
        if (!$value) {
            throw new Exception(trim('Missing required value ' . $key));
        }

        if (is_int($type) && $value != (int)$value) {
            throw new Exception(trim('Invalid required value ' . $key));
        }

        if (is_array($type) && $type && !in_array($value, $type)) {
            throw new Exception(trim('Invalid value ' . $key));
        }

        return $value;
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

if (!function_exists('uuid4')) {
    function uuid4($toString = true) {
        $uuid = \Ramsey\Uuid\Uuid::uuid4();

        if ($toString) {
            return $uuid->toString();
        }

        return $uuid;
    }
}

if (!function_exists('response')) {
    /**
     * @return Response
     */
    function response()
    {
        return context()->getOrCreate(Response::class);
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

if (!function_exists('isValidEmail')) {
    function isValidEmail($email, $dns = false)
    {
        if (!$email) {
            return false;
        }

        if (!strpos($email, '@')) {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if ($dns) {
            [$address, $host] = explode('@', $email);
            $dnsr = checkdnsrr(idn_to_ascii($host, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46) . '.', 'MX');
            return !!$dnsr;
        }

        return true;
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
        $handler = config('pckg.mail.handler', QueueMailHandler::class);

        return Reflect::create($handler)->send($template, $receiver, $data);
    }
}

if (!function_exists('resolve')) {
    function resolve($class, $data = [])
    {
        return Reflect::resolve($class, $data);
    }
}

if (!function_exists('queue')) {
    /**
     * @return Queue
     */
    function queue($channel = null, $command = null, $data = [])
    {
        $queue = context()->getOrCreate(Queue::class);
        if (!$channel) {
            return $queue;
        }

        return $queue->queue($channel, $command, $data);
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
    function chain($chains, $method = 'execute', array $args = [], callable $firstChain = null)
    {
        return (new ChainOfResponsibility($chains, $method, $args, $firstChain))->runChains();
    }
}

/* session */

if (!function_exists('session')) {
    /**
     * @return mixed|Session
     */
    function session($key = null, $default = null)
    {
        $session = context()->getOrCreate(Session::class);

        if (!$key) {
            return $session;
        }

        return $session->get($key, $default);
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

if (!function_exists('oneFrom')) {
    function oneFrom($needle, $haystack, $default)
    {
        if (in_array($needle, $haystack)) {
            return $needle;
        }

        return $default;
    }
}

if (!function_exists('firstWithZero')) {
    function firstWithZero(...$args)
    {
        foreach ($args as $arg) {
            if ($arg || $arg === 0) {
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
        if ($val && $val !== true) {
            context()->getOrCreate(Config::class)->set('path.' . $key, $val);
        }

        $path = config('path.' . $key);

        if ($val === true) {
            $path = str_replace(path('root'), path('ds'), $path);
        }

        return $path;
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

if (!function_exists('uniqueFile')) {
    /**
     * @param $filename
     * @param $dir
     *
     * @return string
     */
    function uniqueFile($filename, $folder)
    {
        $i = 0;
        $newPath = $folder . ($folder ? '/' : '') . $filename;
        while (is_file($newPath)) {
            $newPath = $folder . $i . '_' . $filename;
            $i++;
        }

        return $newPath;
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

if (!function_exists('kaorealpath')) {
    /**
     *
     */
    function kaorealpath($path)
    {
        $explode = explode('/', $path);
        $new = [];
        foreach ($explode as $part) {
            if ($part !== '..') {
                $new[] = $part;
                continue;
            }

            $new = array_slice($new, 0, count($new) - 1);
        }

        return implode('/', $new);
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
        return !request()->server('HTTP_HOST');
        return !isset($_SERVER['HTTP_HOST']);
    }
}

if (!function_exists('isHttp')) {
    function isHttp()
    {
        return !!request()->server('HTTP_HOST');
        return isset($_SERVER['HTTP_HOST']);
    }
}

if (!function_exists('dd')) {
    /**
     * @param mixed ...$mixed
     * @deprecated
     */
    function dd(...$mixed)
    {
        foreach ($mixed as $m) {
            d($m);
        }
        die();
    }
}

if (!function_exists('ddd')) {
    function ddd(...$mixed)
    {
        foreach ($mixed as $m) {
            d($m);
        }
        die();
    }
}

if (!function_exists('d')) {
    function d(...$mixed)
    {
        try {
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
        } catch (Throwable $e) {
        }

        return true;
    }
}

if (!function_exists('db')) {
    function db($depth = 3, $start = 0, $debug = true)
    {
        $db = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $d = [];
        for ($i = $start; $i <= $depth + $start && isset($db[$i]); $i++) {
            if ($debug) {
                d($db[$i]);
            } else {
                $d[] = $db[$i];
            }
        }

        return $d;
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
        $remote = server('REMOTE_ADDR');
        if (!$remote) {
            return false;
        }

        $allowed = config('pckg.framework.dev', []);
        if (in_array($remote, $allowed)) {
            return true;
        }

        foreach ($allowed as $ip) {
            if (substr(strrev($ip), 0, 3) !== '...') {
                continue;
            }
            if (strpos($remote, substr($ip, 0, -3)) === 0) {
                return true;
            }
        }

        return false;
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
    function merge_arrays($to, $merge, $k = null)
    {
        $replace = config('pckg.config.parse.replace', []);

        if ($k && array_preg_match($replace, $k)) {
            return $merge;
        }

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
                if (array_preg_match($replace, $k . '.' . $key)) {
                    $to[$key] = $val;
                    continue;
                }

                $to[$key] = merge_arrays($to[$key], $val, ($k ? $k . '.' . $key : $key));
            }
        }

        return $to;
    }
}

if (!function_exists('array_preg_match')) {
    function array_preg_match($patterns, $subject)
    {
        foreach ($patterns as $pattern) {
            if (preg_match('/' . $pattern . '/', $subject)) {
                return true;
            }
        }

        return false;
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
    function exception(Throwable $e, $parent = false)
    {
        return $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine()
            . ($parent && $e->getPrevious() ? ('(' . exception($e->getPrevious()) . ')') : '');
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

        message('Running in locale ' . $prevLocale . '->' . $locale);
        localeManager()->setCurrent($locale);
        $response = $call();
        message('Changing back ' . $locale . '->' . $prevLocale);
        localeManager()->setCurrent($prevLocale);

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
    function sluggify($str, $separator = '-', $special = null, $limit = 64)
    {
        /**
         * Leave only letters, numbers and special characters
         */
        $str = preg_replace('~[^\\pL0-9 -' . $special . ']+~u', $separator, $str);

        /**
         * Translate special characters.
         */
        $str = iconv("utf-8", "us-ascii//TRANSLIT", $str);

        /**
         * Make them lowercase.
         */
        $str = strtolower($str);

        /**
         * Replace unnecessarry characters.
         */
        $str = preg_replace(['/[^a-zA-Z0-9 -' . $special . ']/', '/[ -]+/', '/^-|-$/'], ['', $separator, ''], $str);

        /**
         * Limit length.
         */
        $str = substr($str, 0, $limit);

        return $str;
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
            [$time1, $time2] = [$time2, $time1];
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
    /**
     * @param null          $key
     * @param callable|null $value
     * @param string        $type
     * @param int           $time
     *
     * @return mixed|Cache
     * @throws Exception
     */
    function cache($key = null, $value = null, $type = 'request', $time = 0)
    {
        $cache = context()->getOrCreate(Cache::class);

        return $key
            ? $cache->cache($key, $value, $type, $time)
            : $cache;
    }
}

if (!function_exists('between')) {
    function between($value, $min, $max)
    {
        $value = (int)$value;
        if ($value < $min) {
            $value = $min;
        } elseif ($value > $max) {
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

if (!function_exists('vueRoute')) {
    function vueRoute(string $route = '', string $component = null, array $tags = [])
    {
        $defaultTags = [
            'vue:route',
            'vue:route:template' => substr($component, 0, 1) !== '<'
                ? '<' . $component . '></' . $component . '>'
                : $component,
        ];

        return route($route, function() use ($tags) {
            $config = config();

            $layout = null;
            $isVue = false;
            foreach ($tags as $tag) {
                if (strpos($tag, 'layout:') === 0) {
                    if (strpos($tag, '-') === false) {
                        $layout = 'Pckg/Generic:' . substr($tag, strlen('layout:'));
                    } else {
                        $layout = substr($tag, strlen('layout:'));
                        $isVue = true;
                    }
                    break;
                }
            }

            if ($isVue) {
                /**
                 * We will parse Vue routes into the frontend layout.
                 * It is currently hardcoded to print <frontend-app></frontend-app> which prints header, body and others.
                 */
                return view('Pckg/Generic/View/frontend', ['content' => '<pckg-app data-frontend></pckg-app>']);
            }

            return view($layout ?? $config->get('pckg.router.layout', 'layout'), ['content' => Vue::getLayout()]);
        })->data([
                     'tags' => $tags ? array_merge($defaultTags, $tags) : $defaultTags,
            'method' => 'GET',
                 ]);
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

if (!function_exists('component')) {
    function component($component, array $params = [])
    {
        $build = '<' . $component . '';
        $generic = null;
        foreach ($params as $k => $v) {
            if (substr($k, 0, 1) === ':') {
                if (!$generic) {
                    $generic = resolve(Generic::class);
                }

                $key = $generic->pushMetadata('router', substr($k, 1), $v);
                $build .= ' ' . $k . '="' . $key . '"';
            } else {
                $build .= ' ' . $k . '="' . $v . '"';
            }
        }
        $build .= '></' . $component . '>';

        return $build;
    }
}

if (!function_exists('price')) {
    function price($price, $currency = null)
    {
        return number($price) . ' ' . ($currency ?? config('pckg.payment.currencySign'));
    }
}

if (!function_exists('number')) {
    function number($price)
    {
        if (is_null($price)) {
            $price = 0.0;
        }

        $localeManager = resolve(Locale::class);

        return number_format(
                $price,
                firstWithZero(config('pckg.locale.decimals'), 2),
                $localeManager->getDecimalPoint(),
                $localeManager->getThousandSeparator()
            );
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
        $file = trim($file);

        if (!$file) {
            return $file;
        }

        if (isRemoteUrl($file)) {
            return $file;
        }

        $host = config('storage.cdn.host');
        if (!$host) {
            return $file;
        }

        return 'https://' . $host . $file;
    }
}

if (!function_exists('isRemoteUrl')) {
    function isRemoteUrl($url)
    {
        $url = trim($url);

        return strpos($url, '//') === 0 || strpos($url, 'https://') === 0 || strpos($url, 'http://') === 0;
    }
}

if (!function_exists('only')) {
    function only($array, $keys, $keepUndefined = true)
    {
        $final = [];

        foreach ($keys as $key) {
            /**
             * Skip not set values (used when patching).
             */
            if (!$keepUndefined && !array_key_exists($key, $array)) {
                continue;
            }
            $final[$key] = $array[$key] ?? null;
        }

        return $final;
    }
}

if (!function_exists('onlyWhen')) {
    function onlyWhen($array, $keys)
    {
        if (!is_array($array) && !is_object($array)) {
            return [];
        }

        $final = [];
        foreach ($keys as $key) {
            if (is_array($array)) {
                if (!array_key_exists($key, $array)) {
                    continue;
                }
                $final[$key] = $array[$key];
            } else {
                if (!$array->{$key}) {
                    continue;
                }

                $final[$key] = $array->{$key};
            }
        }

        return $final;
    }
}

if (!function_exists('throwLogOrContinue')) {
    function throwLogOrContinue(Throwable $e)
    {
        if (dev() || isConsole()) {
            throw $e;
        }
    }
}

if (!function_exists('toSafeFilename')) {
    function toSafeFilename($filename, $special = ' ()\[\]#')
    {
        return substr(preg_replace("/[^A-Za-z0-9\-." . $special . "]/", '', $filename), -200);
    }
}

if (!function_exists('datetime')) {
    function datetime($date, $format = null)
    {
        if (!$format) {
            $format = resolve(Locale::class)->getDatetimeFormat();
        }

        return (new Carbon($date))->format($format);
    }
}

if (!function_exists('datef')) {
    function datef($date, $format = null)
    {
        if (!$format) {
            $format = resolve(Locale::class)->getDateFormat();
        }

        return (new Carbon($date))->format($format);
    }
}

if (!function_exists('timef')) {
    function timef($date, $format = null)
    {
        if (!$format) {
            $format = resolve(Locale::class)->getTimeFormat();
        }

        return (new Carbon($date))->format($format);
    }
}

if (!function_exists('sha1random')) {
    function sha1random()
    {
        return sha1(\Defuse\Crypto\Key::createNewRandomKey()->saveToAsciiSafeString());
    }
}

if (!function_exists('filename')) {
    function filename($file)
    {
        $exploded = explode('/', $file);
        return end($exploded);
    }
}

if (!function_exists('escapeshellargs')) {
    function escapeshellargs($data)
    {
        $parameters = [];
        foreach ($data as $key => $val) {
            if (is_int($key)) {
                /**
                 * We're passing attribute, option without value or already encoded part of command.
                 */
                $parameters[] = $val;
            } elseif (is_array($val)) {
                /**
                 * Array of values should be handled differently.
                 */
                if (isArrayList($val)) {
                    foreach ($val as $subval) {
                        $parameters[] = '--' . $key . '=' . escapeshellarg($subval);
                    }
                } else {
                    $parameters[] = '--' . $key . '=' . escapeshellarg(json_encode($val));
                }
            } elseif (is_object($val)) {
                /**
                 * Serialize object.
                 */
                $parameters[] = '--' . $key . '=' . escapeshellarg(base64_encode(serialize($val)));
            } else {
                /**
                 * We simply escape all other values.
                 */
                $parameters[] = '--' . $key . '=' . escapeshellarg($val);
            }
        }

        return $parameters;
    }
}

if (!function_exists('seededShuffle')) {
    function seededShuffle($seed = null, $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $items = str_split($chars, 1);
        mt_srand($seed ? $seed : time());
        for ($i = count($items) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
        }

        return implode($items);
    }
}

if (!function_exists('seededHash')) {
    function seededHash(int $seed, $length)
    {
        $shuffled = seededShuffle($seed);

        $items = str_split($shuffled, 1);
        mt_srand($seed ? $seed : (int)(microtime(true) * 1000));

        $hash = [];
        while (count($hash) < $length) {
            $hash[] = $items[mt_rand(0, count($items) - 1)];
        }

        return implode($hash);
    }
}

if (!function_exists('str2int')) {
    function str2int($string, $max = PHP_INT_MAX)
    {
        $array = str_split($string, 1);
        $sum = 0.0;
        foreach ($array as $i => $char) {
            mt_srand(($i + 1) / ord($char) * 10000);
            $rand = mt_rand(1, 10000);
            $sum += 1 / $rand;
        }

        $range = $sum - floor($sum);

        return (int)floor($range * $max);
    }
}

if (!function_exists('int2str')) {
    function int2str(int $int, $length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        $max = strlen($chars);
        $str = '';
        while ($int >= $max) {
            $mod = $int % $max;
            $str = $chars[$mod] . $str;
            $int = (int)(($int - $mod) / $max);
        }
        $str = $chars[$int] . $str;

        return str_pad($str, $length, 'a', STR_PAD_LEFT);
    }
}

if (!function_exists('numequals')) {
    function numequals($a, $b)
    {
        return abs((float)$a - (float)$b) < PHP_FLOAT_EPSILON;
    }
}

if (!function_exists('appendIf')) {
    function appendIf($is, $append) {
        return $is ? $is . $append : '';
    }
}

if (!function_exists('prependIf')) {
    function prependIf($prepend, $is) {
        return $is ? $prepend . $is : '';
    }
}
