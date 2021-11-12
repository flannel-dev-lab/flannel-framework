<?php

// For the installation script
define("_IS_INSTALLED", false);

define('DS', DIRECTORY_SEPARATOR);
define('IS_CLI', strtolower(php_sapi_name())==='cli');
define('IS_WINDOWS', strtolower(substr(PHP_OS, 0, 3))==='win');
define('IS_HTTPS', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on');

define('SECONDS_PER_HOUR', 3600);
define('SECONDS_PER_DAY',  86400);
define('SECONDS_PER_WEEK', 604800);
define('SECONDS_PER_YEAR', 31536000);
ini_set('date.timezone', 'UTC');

// Securing Sessions
ini_set('session.name', 'sid');
ini_set('session.auto_start', 'Off');
ini_set('session.use_strict_mode', 'On');
ini_set('session.hash_function', 'sha256');     //PHP 5-7.1.0 only
ini_set('session.sid_bits_per_character', 6);   //PHP 7.1.0+
ini_set('session.sid_length', 48);              //PHP 7.1.0+
ini_set('session.cookie_lifetime', 0);
ini_set('session.cookie_httponly', 'On');
ini_set('session.cache_limiter', 'nocache');
