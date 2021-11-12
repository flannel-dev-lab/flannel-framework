<?php

define('ROOT_DIR', dirname(__FILE__) . "/../..");

require_once ROOT_DIR . '/env.php';

// Include Composer first
require_once ROOT_DIR . '/vendor/autoload.php';

// $_POST['config_file'] . "/conf.php"
$templateContent = file_get_contents("../../conf/conf.php.template");
$templateContent = str_replace('YOUR_USERNAME', $_POST['config_file'], $templateContent);

if (!file_put_contents("../../conf/conf.php", $templateContent)) {
    echo "Failed to write to a config file.";
    die();
}

$environmentContent = file_get_contents(ROOT_DIR . "/env.php");
$environmentContent = str_replace('define("_IS_INSTALLED", false);', 'define("_IS_INSTALLED", true);', $environmentContent);
file_put_contents(ROOT_DIR . "/env.php", $environmentContent);

//unlink("config_setup.php");

header("Location: /admin/auth/login");
die();
