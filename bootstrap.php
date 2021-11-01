<?php

/**
 * Flannel Core Framework
 *
 * @package  Flannel
 * @author   Derek Sanford <derek@flanneldevlab.com>
 */

if (!defined('INTERNAL_SCRIPT')) {
    define('INTERNAL_SCRIPT', true);
}

define('ROOT_DIR', dirname(__FILE__));

require_once ROOT_DIR . '/env.php';

/**
 * Autoloaders
 */

// Include Composer first
require_once ROOT_DIR . '/vendor/autoload.php';

// Include Flannel resources
require_once ROOT_DIR . '/vendor/Flannel/Core/Autoloader.php';
\Flannel\Core\Autoloader::register();
\Flannel\Core\Autoloader::addPath(ROOT_DIR.'/app');

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

// Load additional bootstrap files
foreach (glob(ROOT_DIR . '/bootstrap/*.php') as $filename) {
    require_once($filename);
}
