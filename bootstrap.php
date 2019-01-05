<?php

/**
 * Flannel Core Framework
 *
 * @package  Flannel
 * @author   Derek Sanford <derek@flanneldevlab.com>
 */

define('ROOT_DIR', dirname(__FILE__));

require_once ROOT_DIR . '/env.php';

/**
 * Autoloaders
 */
require_once ROOT_DIR . '/vendor/Flannel/Core/Autoloader.php';
\Flannel\Core\Autoloader::register();
\Flannel\Core\Autoloader::addPath(ROOT_DIR.'/app');
\Flannel\Core\Autoloader::addPath(ROOT_DIR.'/vendor');
\Flannel\Core\Autoloader::addPath(ROOT_DIR.'/vendor/Flannel/Core/Autoload');

/**
 * Configurations
 */
require_once ROOT_DIR.'/conf/default.php';
require_once ROOT_DIR.'/conf/conf.php';
\Flannel\Core\Config::lock();
define('BASE_URL', \Flannel\Core\Config::get('env.base_url'));

/**
 * Error handlers
 */
Flannel\Core\ErrorHandler::init();
set_error_handler(['\\Flannel\\Core\\ErrorHandler', 'log']);
register_shutdown_function(['\\Flannel\\Core\\ErrorHandler', 'shutdown']);

// Load sentry.io if enabled
if (\Flannel\Core\Config::get('sentry.enabled')) {
    $client = new Raven_Client(\Flannel\Core\Config::get('sentry.url'));
    $client->setEnvironment(\Flannel\Core\Config::get('sentry.environment'));
    $error_handler = new Raven_ErrorHandler($client);
    $error_handler->registerExceptionHandler();
    $error_handler->registerErrorHandler();
    $error_handler->registerShutdownFunction();
}
