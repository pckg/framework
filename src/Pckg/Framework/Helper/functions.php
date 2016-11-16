<?php

use DebugBar\DebugBar;
use Derive\Platform\Entity\Platforms;
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
use Pckg\Framework\Request\Data\Session;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Pckg\Framework\View\Twig;
use Pckg\Htmlbuilder\Element\Form;
use Pckg\Manager\Asset;
use Pckg\Manager\Vue;
use Pckg\Queue\Service\Queue;
use Pckg\Translator\Service\Translator;

/* context */

/**
 * @return Context
 * @throws Exception
 */
function context()
{
    return Context::getInstance();
}

/**
 * @return Environment
 */
function env()
{
    return context()->get(Environment::class);
}

function object_implements($object, $interface)
{
    return in_array($interface, class_implements($object));
}

/**
 * @return Application
 */
function app()
{
    return context()->get(Application::class);
}

/**
 * @return Request
 */
function request()
{
    return context()->get(Request::class);
}

/**
 * @return Request
 */
function post()
{
    return request()->post();
}

/**
 * @return Auth
 */
function auth()
{
    return context()->getOrCreate(Auth::class);
}

/**
 * @return Response
 */
function response()
{
    return context()->get(Response::class);
}

function redirect($url = null)
{
    return response()->redirect($url);
}

/**
 * @param null $entity
 *
 * @return \Pckg\Database\Entity
 */
function entity($entity = null)
{
    return context()->getEntity($entity);
}

/**
 * @param null $form
 *
 * @return \Htmlbuilder\Element\Form
 */
function form($form = null)
{
    return context()->getForm($form);
}

/**
 * @param $factory
 *
 * @return \Pckg\Concept\Factory
 */
function factory($factory)
{
    return context()->getFactory($factory);
}

/* event */

/**
 *
 * @return Dispatcher
 * */
function dispatcher()
{
    return context()->get(Dispatcher::class);
}

/**
 * @param       $event
 * @param null  $method
 * @param array $args
 *
 * @return mixed|null|object
 */
function trigger($event, array $args = [], $method = null)
{
    return dispatcher()->trigger($event, $args, $method);
}

/**
 * @param \Event $event
 * @param        $strtotime
 */
function schedule(AbstractEvent $event, $strtotime)
{
    // Event::schedule($event, $strtotime);
}

/**
 *
 * @return Pckg\Concept\Event\Dispatcher
 * */
function listen($event, $eventHandler)
{
    return dispatcher()->listen($event, $eventHandler);
}

function listenOnce($event, $eventHandler)
{
    if (dispatcher()->hasListener($event, $eventHandler)) {
        return;
    }

    return dispatcher()->listen($event, $eventHandler);
}

function registerEvent(AbstractEvent $event)
{
    return dispatcher()->registerEvent($event);
}

function triggerEvent($event, $args = [])
{
    return dispatcher()->trigger($event, $args, 'handle');
}

/* router */

/**
 * @return \Pckg\Framework\Router
 */
function router()
{
    return context()->get(Router::class);
}

/**
 * @param       $url
 * @param array $params
 *
 * @return string
 */
function url($url, $params = [], $absolute = false)
{
    try {
        $url = router()->make($url, $params, $absolute);

        return $url;
    } catch (Exception $e) {
        if (prod()) {
            return null;
        }

        return exception($e);
    }
}

function resolve($class)
{
    return context()->getOrCreate($class);
}

/**
 * @return Queue
 */
function queue()
{
    return context()->getOrCreate(Queue::class);
}

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

/* session */

/**
 * @return mixed
 */
function session()
{
    return context()->getOrCreate(Session::class);
}

/**
 * @param      $key
 * @param null $val
 *
 * @return mixed
 */
function flash($key, $val = null)
{
    return $val
        ? session()->setFlash($key, $val)
        : session()->getFlash($key);
}

/* config */

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

/**
 * @param      $key
 * @param null $default
 *
 * @return null
 * @deprecated
 */
function conf($key, $default = null)
{
    return config($key) ?: $default;
}

function platform()
{
    return Platforms::current();
}

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

/* quick helpers */

function __i18n($key, $data = [], $lang = null)
{
    try {
        $translator = context()->getOrCreate(Translator::class);

        $translation = trim($translator->get($key, $lang));

        return $data
            ? (new Twig(null, $data))->setTemplate($translation)->autoparse()
            : $translation;
    } catch (Exception $e) {
        return $key;
    }
}

if (!function_exists('__')) {
    function __($key, $data = [], $lang = null)
    {
        return __i18n($key, $data, $lang);
    }
}

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

/**
 * @param       $view
 * @param array $data
 *
 * @return Twig
 */
function view($view, $data = [], $assets = [])
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
            dirname(debug_backtrace()[0]['file']) . path('ds') . '..' . path('ds') . '..' . path('ds') . '..' . path(
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

/**
 * @return Asset
 */
function assetManager()
{
    return context()->getOrCreate(Asset::class);
}

/**
 * @return Asset
 */
function vueManager()
{
    return context()->getOrCreate(Vue::class);
}

function assets($assets)
{
    assetManager()->addAssets($assets);
}

/**
 * @return mixed
 */
function autoloader()
{
    return require BASE_PATH . "vendor/autoload.php";
}

function isConsole()
{
    return !isset($_SERVER['HTTP_HOST']);
}

function isHttp()
{
    return isset($_SERVER['HTTP_HOST']);
}

function dd(...$mixed)
{
    foreach ($mixed as $m) {
        d($m);
    }
    die();
}

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

function db($depth = 3, $start = 0)
{
    $db = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    for ($i = $start; $i <= $depth + $start && isset($db[$i]); $i++) {
        d($db[$i]);
    }
}

function dev()
{
    return env()->isDev();
}

function prod()
{
    return env()->isPro();
}

function win()
{
    return env()->isWin();
}

function unix()
{
    return env()->isUnix();
}

/**
 * @return DebugBar
 */
function debugBar()
{
    return context()->exists(DebugBar::class)
        ? context()->get(DebugBar::class)
        : null;
}

function message($message)
{
    if ($debugBar = debugBar()) {
        $debugBar->getCollector('messages')->addMessage($message);
    }
}

function measure($message, callable $callback)
{
    startMeasure($message);
    $result = $callback();
    stopMeasure($message);

    return $result;
}

function startMeasure($name)
{
    if ($debugBar = debugBar()) {
        try {
            $debugBar['time']->startMeasure($name);
        } catch (Exception $e) {
            // fail silently
        }
    }
}

function stopMeasure($name)
{
    if ($debugBar = debugBar()) {
        try {
            $debugBar['time']->stopMeasure($name);
        } catch (Exception $e) {
            // fail silently
        }
    }
}

function collect($data)
{
    return new Collection($data);
}

function array_merge_array($merge, $to)
{
    foreach ($to as $key => &$val) {
        $val = array_merge($merge, $val);
    }

    return $to;
}

function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

/**
 * @param Exception $e
 *
 * @return string
 */
function exception(Exception $e)
{
    return $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine();
}

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

function media($name, $dir = null, $relative = true, $base = null)
{
    return img($name, $dir, $relative, $base);
}

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