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
 * We automatically hide errors and enable caching.
 */
$environment = $context->createEnvironment(Pckg\Framework\Environment\Production::class);

/**
 * Create application.
 * We will use config/router.php for proper loading.
 */
$application = $context->createWebsiteApplication();

/**
 * Initialize application.
 * This will parse config, set localization 'things', estamblish connection to database, initialize and register
 * routes, set application autoloaders and providers, session, response, request and assets.
 */
$application->init();

/**
 * Run applications.
 * Everything was preset, we need to run request and return response.
 */
$application->run();