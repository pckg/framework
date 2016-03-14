<?php

use Pckg\Concept\ChainOfResponsibility;
use Pckg\Concept\Context;
use Pckg\Concept\Event\AbstractEvent;
use Pckg\Collection;
use Pckg\Framework\Config;
use Pckg\Framework\Lang;
use Pckg\Framework\View\Twig;
use Pckg\Htmlbuilder\Element\Form;

/* context */

/**
 * @return Context
 * @throws \Exception
 */
function context()
{
    return Context::getInstance();
}

/**
 * @return \Pckg\Framework\Environment
 */
function env()
{
    return context()->get('Environment');
}

/**
 * @return \Pckg\Framework\Application
 */
function app()
{
    return context()->get('Application');
}

/**
 * @return \Pckg\Framework\Request
 */
function request()
{
    return context()->get('Request');
}

/**
 * @return \Pckg\Framework\Response
 */
function response()
{
    return context()->get('Response');
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
 * @return Pckg\Concept\Event\Dispatcher
 * */
function dispatcher()
{
    return context()->get('Dispatcher');
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
    return context()->get('Router');
}

/**
 * @param       $url
 * @param array $params
 *
 * @return string
 */
function url($url, $params = [])
{
    return router()->make($url, $params);
}

/**
 * @param       $chains
 * @param null  $method
 * @param array $args
 * @param null  $firstChain
 *
 * @return mixed|null|object
 * @throws \Exception
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
    return context()->get('Config')->get($key);
}

/**
 * @param      $key
 * @param null $val
 *
 * @return array|null
 */
function path($key, $val = null)
{
    if ($val) {
        context()->getOrCreate('Config', Config::class)->set('path.' . $key, $val);
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
    }

    return $view;
}

/**
 * @return mixed
 */
function autoloader()
{
    return require "../vendor/autoload.php";
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
    $db = debug_backtrace();
    echo 'Debuck backtrace result';
    for ($i = $start; $i <= $depth + $start && isset($db[$i]); $i++) {
        d($db[$i]);
    }
}

function dev()
{
    env()->isDev();
}

function prod()
{
    env()->isPro();
}

/**
 * @return DebugBar
 */
function debugBar()
{
    return context()->exists('DebugBar')
        ? context()->get('DebugBar')
        : null;
}

function startMeasure($name)
{
    if ($debugBar = debugBar()) {
        $debugBar['time']->startMeasure($name);
    }
}

function stopMeasure($name)
{
    if ($debugBar = debugBar()) {
        try {
            $debugBar['time']->stopMeasure($name);
        } catch (\Exception $e) {

        }
    }
}

function collect($data)
{
    return new Collection($data);
}

function array_merge_array($merge, $to)
{
    foreach ($to as &$val) {
        $val = array_merge($merge, $to);
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