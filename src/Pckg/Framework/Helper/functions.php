<?php

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
use Pckg\Framework\Lang;
use Pckg\Framework\Request;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Pckg\Framework\View\Twig;
use Pckg\Htmlbuilder\Element\Form;
use Pckg\Queue\Service\Queue;

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

function object_implements($object, $interface){
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
    return router()->make($url, $params, $absolute);
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
    return context()->session();
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
        ? content()->session()->setFlash($key, $val)
        : content()->session()->getFlash($key);
}

/* config */

/**
 * @param $text
 *
 * @return array|null
 */
function config($key = null)
{
    return context()->get(Config::class)->get($key);
}

function conf($key, $default = null)
{
    return config($key) ?: $default;
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
function __($key, $lang = null)
{
    return $key;

    return Lang::get($key, $lang);
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
function view($view, $data = [])
{
    $view = new Twig($view, $data);
    if ($parent = realpath(dirname(debug_backtrace()[0]['file']) . '/../View/')) {
        $view->addDir($parent, Twig::PRIORITY_LAST);
        $view->addDir(realpath(dirname(debug_backtrace()[0]['file']) . '/../../../'), Twig::PRIORITY_LAST);
    }

    return $view;
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
        $val = array_merge($val, $merge);
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
    return $e->getMessage() . '@' . $e->getFile() . ':' . $e->getLine();
}