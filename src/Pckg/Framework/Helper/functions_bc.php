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
use Pckg\Framework;

if (!function_exists('env')) {
    /**
     * @return Environment
     */
    function env()
    {
        return Framework\Helper\env();
    }
}

/**
 * @return mixed|string|array
 */
if (!function_exists('dotenv')) {
    function dotenv($key, $default = null)
    {
        return Framework\Helper\dotenv($key, $default);
    }
}

/**
 * @return Application
 */
if (!function_exists('app')) {
    function app()
    {
        return Framework\Helper\app();
    }
}

if (!function_exists('getDotted')) {
    function getDotted($data, $keys, $i = 0, $default = null)
    {
        return Framework\Helper\getDotted($data, $keys, $i, $default);
    }
}

if (!function_exists('hasDotted')) {
    function hasDotted($data, $keys, $i = 0)
    {
        return Framework\Helper\hasDotted($data, $keys, $i);
    }
}

if (!function_exists('retry')) {
    function retry(callable $task, int $times = null, callable $onError = null, $interval = null)
    {
        return Framework\Helper\retry($task, $times, $onError, $interval);
    }
}

if (!function_exists('request')) {
    /**
     * @return Request
     */
    function request()
    {
        return Framework\Helper\request();
    }
}

/**
 * @return Request\Data\Post|mixed|string|null|array
 */
if (!function_exists('post')) {
    function post($key = null, $default = [])
    {
        return Framework\Helper\post($key, $default);
    }
}

/**
 * @return Request|mixed
 */
if (!function_exists('get')) {
    function get($key = null, $default = [])
    {
        return Framework\Helper\get($key, $default);
    }
}

/**
 * @return Request|mixed
 */
if (!function_exists('server')) {
    function server($key = null, $default = [])
    {
        return Framework\Helper\server($key, $default);
    }
}

/**
 * @return Request|mixed
 */
if (!function_exists('cookie')) {
    function cookie($key = null, $default = [])
    {
        return Framework\Helper\cookie($key, $default);
    }
}

/**
 * @return Request
 */
if (!function_exists('files')) {
    function files($key = null, $default = [])
    {
        return Framework\Helper\files($key, $default);
    }
}

/**
 * @return mixed
 */
if (!function_exists('required')) {
    function required($value, $type = null, $key = null)
    {
        return Framework\Helper\required($value, $type, $key);
    }
}

if (!function_exists('auth')) {
    /**
     * @return Auth
     */
    function auth($provider = null)
    {
        return Framework\Helper\auth($provider);
    }
}

if (!function_exists('uuid4')) {
    function uuid4($toString = true)
    {
        return Framework\Helper\uuid4($toString);
    }
}

if (!function_exists('response')) {
    /**
     * @return Response
     */
    function response()
    {
        return Framework\Helper\response();
    }
}

if (!function_exists('redirect')) {
    function redirect($url = null)
    {
        return Framework\Helper\redirect($url);
    }
}

if (!function_exists('internal')) {
    function internal($url = null)
    {
        return Framework\Helper\internal($url);
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
        return Framework\Helper\entity($entity);
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
        return Framework\Helper\form($form);
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
        return Framework\Helper\factory($factory);
    }
}

if (!function_exists('schedule')) {
    /**
     * @param \Event $event
     * @param        $strtotime
     */
    function schedule(AbstractEvent $event, $strtotime)
    {
        return Framework\Helper\schedule($event, $strtotime);
    }
}

if (!function_exists('isValidEmail')) {
    function isValidEmail($email, $dns = false)
    {
        return Framework\Helper\isValidEmail($email, $dns);
    }
}

/* router */

if (!function_exists('router')) {
    /**
     * @return \Pckg\Framework\Router
     */
    function router()
    {
        return Framework\Helper\router();
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
        return Framework\Helper\url($url, $params, $absolute, $envPrefix);
    }
}

if (!function_exists('email')) {
    function email($template, $receiver, $data = [])
    {
        return Framework\Helper\email($template, $receiver, $data);
    }
}

if (!function_exists('resolve')) {
    function resolve($class, $data = [])
    {
        return Framework\Helper\resolve($class, $data);
    }
}

if (!function_exists('queue')) {
    /**
     * @return Queue
     */
    function queue($channel = null, $command = null, $data = [])
    {
        return Framework\Helper\queue($channel, $command, $data);
    }
}

if (!function_exists('chain')) {
    /**
     * @param       $chains
     * @param null $method
     * @param array $args
     * @param null $firstChain
     *
     * @return mixed|null|object
     * @throws Exception
     */
    function chain($chains, $method = 'execute', array $args = [], callable $firstChain = null)
    {
        return Framework\Helper\chain($chains, $method, $args, $firstChain);
    }
}

/* session */

if (!function_exists('session')) {
    /**
     * @return mixed|Session
     */
    function session($key = null, $default = null)
    {
        return Framework\Helper\session($key, $default);
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
        return Framework\Helper\flash($key, $val);
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
        return Framework\Helper\config($key, $default);
    }
}

if (!function_exists('first')) {
    function first(...$args)
    {
        return Framework\Helper\first(...$args);
    }
}

if (!function_exists('oneFrom')) {
    function oneFrom($needle, $haystack, $default)
    {
        return Framework\Helper\oneFrom($needle, $haystack, $default);
    }
}

if (!function_exists('firstWithZero')) {
    function firstWithZero(...$args)
    {
        return Framework\Helper\firstWithZero(...$args);
    }
}

if (!function_exists('path')) {
    /**
     * @param      $key
     * @param null $val
     *
     * @return string
     */
    function path($key = null, $val = null)
    {
        return Framework\Helper\path($key, $val);
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
        return Framework\Helper\relativePath($key);
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
        return Framework\Helper\uniqueFile($filename, $folder);
    }
}

/* quick helpers */

if (!function_exists('__i18n')) {
    function __i18n($key, $data = [], $lang = null)
    {
        return Framework\Helper\__i18n($key, $data, $lang);
    }
}

if (!function_exists('__')) {
    function __($key, $data = [], $lang = null)
    {
        return Framework\Helper\__($key, $data, $lang);
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
        return Framework\Helper\toCamel($text);
    }
}

if (!function_exists('kaorealpath')) {
    /**
     *
     */
    function kaorealpath($path)
    {
        return Framework\Helper\kaorealpath($path);
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
        return Framework\Helper\view($view, $data, $assets);
    }
}

if (!function_exists('assetManager')) {
    /**
     * @return Asset
     */
    function assetManager()
    {
        return Framework\Helper\assetManager();
    }
}

if (!function_exists('vueManager')) {
    /**
     * @return Vue
     */
    function vueManager()
    {
        return Framework\Helper\vueManager();
    }
}

if (!function_exists('seoManager')) {
    /**
     * @return Seo
     */
    function seoManager()
    {
        return Framework\Helper\seoManager();
    }
}

if (!function_exists('localeManager')) {
    /**
     * @return Locale
     */
    function localeManager()
    {
        return Framework\Helper\localeManager();
    }
}

if (!function_exists('metaManager')) {
    /**
     * @return Meta
     */
    function metaManager()
    {
        return Framework\Helper\metaManager();
    }
}

if (!function_exists('gtmManager')) {
    /**
     * @return Gtm
     */
    function gtmManager()
    {
        return Framework\Helper\gtmManager();
    }
}

if (!function_exists('assets')) {
    function assets($assets)
    {
        Framework\Helper\assets($assets);
    }
}

if (!function_exists('autoloader')) {
    /**
     * @return mixed
     */
    function autoloader()
    {
        return Framework\Helper\autoloader();
    }
}

if (!function_exists('isConsole')) {
    function isConsole()
    {
        return Framework\Helper\isConsole();
    }
}

if (!function_exists('isHttp')) {
    function isHttp()
    {
        return Framework\Helper\isHttp();
    }
}

if (!function_exists('dd')) {
    /**
     * @param mixed ...$mixed
     * @deprecated
     */
    function dd(...$mixed)
    {
        Framework\Helper\dd(...$mixed);
    }
}

if (!function_exists('ddd')) {
    function ddd(...$mixed)
    {
        Framework\Helper\ddd(...$mixed);
    }
}

if (!function_exists('d')) {
    function d(...$mixed)
    {
        Framework\Helper\d(...$mixed);
    }
}

if (!function_exists('db')) {
    function db($depth = 3, $start = 0, $debug = true)
    {
        return Framework\Helper\db($depth, $start, $debug);
    }
}

if (!function_exists('dev')) {
    function dev()
    {
        return Framework\Helper\dev();
    }
}

if (!function_exists('prod')) {
    function prod()
    {
        return Framework\Helper\prod();
    }
}

if (!function_exists('implicitDev')) {
    function implicitDev()
    {
        return Framework\Helper\implicitDev();
    }
}

if (!function_exists('win')) {
    function win()
    {
        return Framework\Helper\win();
    }
}

if (!function_exists('unix')) {
    function unix()
    {
        return Framework\Helper\unix();
    }
}

if (!function_exists('message')) {
    function message($message, $collector = 'messages')
    {
        Framework\Helper\message($message, $collector);
    }
}

if (!function_exists('array_merge_array')) {
    function array_merge_array($merge, $to)
    {
        return Framework\Helper\array_merge_array($merge, $to);
    }
}

if (!function_exists('merge_arrays')) {
    function merge_arrays($to, $merge, $k = null)
    {
        return Framework\Helper\merge_arrays($to, $merge, $k);
    }
}

if (!function_exists('array_preg_match')) {
    function array_preg_match($patterns, $subject)
    {
        return Framework\Helper\array_preg_match($patterns, $subject);
    }
}

if (!function_exists('str_lreplace')) {
    function str_lreplace($search, $replace, $subject)
    {
        return Framework\Helper\str_lreplace($search, $replace, $subject);
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
        return Framework\Helper\exception($e, $parent);
    }
}

if (!function_exists('img')) {
    function img($name, $dir = null, $relative = true, $base = null)
    {
        return Framework\Helper\img($name, $dir, $relative, $base);
    }
}

if (!function_exists('media')) {
    function media($name, $dir = null, $relative = true, $base = null)
    {
        return Framework\Helper\media($name, $dir, $relative, $base);
    }
}

if (!function_exists('runInLocale')) {
    function runInLocale($call, $locale)
    {
        return Framework\Helper\runInLocale($call, $locale);
    }
}

if (!function_exists('isArrayList')) {
    function isArrayList($array)
    {
        return Framework\Helper\isArrayList($array);
    }
}

if (!function_exists('sluggify')) {
    function sluggify($str, $separator = '-', $special = null, $limit = 64)
    {
        return Framework\Helper\sluggify($str, $separator, $special, $limit);
    }
}

if (!function_exists('get_date_diff')) {
    function get_date_diff($time1, $time2, $precision = 2)
    {
        return Framework\Helper\get_date_diff($time1, $time2, $precision);
    }
}

if (!function_exists('br2nl')) {
    function br2nl($string)
    {
        return Framework\Helper\br2nl($string);
    }
}

if (!function_exists('array_union')) {
    function array_union($one, $two)
    {
        return Framework\Helper\array_union($one, $two);
    }
}

if (!function_exists('transform')) {
    function transform($collection, $rules)
    {
        return Framework\Helper\transform($collection, $rules);
    }
}

if (!function_exists('cache')) {
    /**
     * @param null $key
     * @param callable|null $value
     * @param string $type
     * @param int $time
     *
     * @return mixed|Cache|array|string
     * @throws Exception
     */
    function cache($key = null, $value = null, $type = 'request', $time = 0)
    {
        return Framework\Helper\cache($key, $value, $type, $time);
    }
}

if (!function_exists('between')) {
    function between($value, $min, $max)
    {
        return Framework\Helper\between($value, $min, $max);
    }
}

if (!function_exists('route')) {
    function route($route = '', $view = 'index', $controller = null)
    {
        return Framework\Helper\route($route, $view, $controller);
    }
}

if (!function_exists('vueRoute')) {
    /**
     * @param string $route
     * @param string|null $component
     * @param array $tags
     * @param array $children
     * @return Router\Route\Route|Router\Route\VueRoute
     */
    function vueRoute(string $route = '', string $component = null, array $tags = [], array $children = [])
    {
        return Framework\Helper\vueRoute($route, $component, $tags, $children);
    }
}

if (!function_exists('routeGroup')) {
    function routeGroup($data = [], $routes = [])
    {
        return Framework\Helper\routeGroup($data, $routes);
    }
}

if (!function_exists('component')) {
    function component($component, array $params = [])
    {
        return Framework\Helper\component($component, $params);
    }
}

if (!function_exists('price')) {
    function price($price, $currency = null)
    {
        return Framework\Helper\price($price, $currency);
    }
}

if (!function_exists('number')) {
    function number($price)
    {
        return Framework\Helper\number($price);
    }
}

if (!function_exists('is_associative_array')) {
    function is_associative_array($array)
    {
        return Framework\Helper\is_associative_array($array);
    }
}

if (!function_exists('strbetween')) {
    function strbetween($text, $from, $to)
    {
        return Framework\Helper\strbetween($text, $from, $to);
    }
}

if (!function_exists('cdn')) {
    function cdn($file)
    {
        return Framework\Helper\cdn($file);
    }
}

if (!function_exists('isRemoteUrl')) {
    function isRemoteUrl($url)
    {
        return Framework\Helper\isRemoteUrl($url);
    }
}

if (!function_exists('only')) {
    function only($array, $keys, $keepUndefined = true)
    {
        return Framework\Helper\only($array, $keys, $keepUndefined);
    }
}

if (!function_exists('onlyFromRequest')) {
    function onlyFromRequest(array $data, string $key = null)
    {
        return Framework\Helper\onlyFromRequest($data, $key);
    }
}

if (!function_exists('onlyWhen')) {
    function onlyWhen($array, $keys)
    {
        return Framework\Helper\onlyWhen($array, $keys);
    }
}

if (!function_exists('throwLogOrContinue')) {
    function throwLogOrContinue(Throwable $e)
    {
        Framework\Helper\throwLogOrContinue($e);
    }
}

if (!function_exists('toSafeFilename')) {
    function toSafeFilename($filename, $special = ' ()\[\]#')
    {
        return Framework\Helper\toSafeFilename($filename, $special);
    }
}

if (!function_exists('datetime')) {
    function datetime($date, $format = null)
    {
        return Framework\Helper\datetime($date, $format);
    }
}

if (!function_exists('datef')) {
    function datef($date, $format = null)
    {
        return Framework\Helper\datef($date, $format);
    }
}

if (!function_exists('timef')) {
    function timef($date, $format = null)
    {
        return Framework\Helper\timef($date, $format);
    }
}

if (!function_exists('sha1random')) {
    function sha1random()
    {
        return Framework\Helper\sha1random();
    }
}

if (!function_exists('randomKey')) {
    function randomKey()
    {
        return Framework\Helper\randomKey();
    }
}

if (!function_exists('filename')) {
    function filename($file)
    {
        return Framework\Helper\filename($file);
    }
}

if (!function_exists('escapeshellargs')) {
    function escapeshellargs($data)
    {
        return Framework\Helper\escapeshellargs($data);
    }
}

if (!function_exists('seededShuffle')) {
    function seededShuffle($seed = null, $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        return Framework\Helper\seededShuffle($seed, $chars);
    }
}

if (!function_exists('seededHash')) {
    function seededHash(int $seed, $length)
    {
        return Framework\Helper\seededHash($seed, $length);
    }
}

if (!function_exists('str2int')) {
    function str2int($string, $max = PHP_INT_MAX)
    {
        return Framework\Helper\str2int($string, $max);
    }
}

if (!function_exists('int2str')) {
    function int2str(int $int, $length = 8)
    {
        return Framework\Helper\int2str($int, $length);
    }
}

if (!function_exists('numequals')) {
    function numequals($a, $b)
    {
        return Framework\Helper\numequals($a, $b);
    }
}

if (!function_exists('encryptBlob')) {
    function encryptBlob($plaintext, $password = null)
    {
        return Framework\Helper\encryptBlob($plaintext, $password);
    }
}

if (!function_exists('decryptBlob')) {
    function decryptBlob($ciphertext, $password = null)
    {
        return Framework\Helper\decryptBlob($ciphertext, $password);
    }
}

if (!function_exists('appendIf')) {
    function appendIf($is, $append)
    {
        return Framework\Helper\appendIf($is, $append);
    }
}

if (!function_exists('prependIf')) {
    function prependIf($prepend, $is)
    {
        return Framework\Helper\prependIf($prepend, $is);
    }
}

if (!function_exists('forward')) {
    function forward($input, callable $task)
    {
        return Framework\Helper\forward($input, $task);
    }
}
