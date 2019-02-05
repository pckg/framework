<?php

/**
 * Define base path if it's not defined yet.
 * We need to know this because it's used in a lot of things.
 */
if (!defined('BASE_PATH')) {
    define('BASE_PATH', defined('__ROOT__') ? __ROOT__ : realpath(__DIR__ . '/../../../..') . '/');
}
/**
 * Require autoloader which will take care of loading classes.
 */
require_once BASE_PATH . "vendor/autoload.php";

/**
 * Create context instance.
 * This is actually dependency container.
 */
$context = Pckg\Framework\Helper\Context::createInstance();

/**
 * Create and bind environment, console or website app, init and run it.
 */
$context->boot(Pckg\Framework\Environment\Queue::class);
