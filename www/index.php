<?php

// Use this for any tracing times needed
define('GLOBAL_START_TIMESTAMP', microtime(true));
define('INTERNAL_SCRIPT', false);

require_once '../bootstrap.php';

Flannel\Core\Router::run('Controller_Error_Code');
