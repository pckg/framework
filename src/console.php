<?php

/**
 * Define base path if it's not defined yet.
 * We need to know this because it's used in a lot of things.
 */
if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/../../../..') . '/');
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
 * Create development environment.
 * We automatically display errors and load debugbar.
 */
$environment = $context->createEnvironment(Pckg\Framework\Environment\Development::class);

/**
 * Create application.
 * It should be passed as parameter.
 */
$application = $context->createConsoleApplication();

/**
 * Initialize application.
 * This will parse config, set localization 'things', estamblish connection to database,
 * set application autoloaders and providers.
 */
$application->init();

/**
 * Run applications.
 * Everything was preset, we need to run command.
 */
$application->run();
