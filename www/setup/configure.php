<?php

define('ROOT_DIR', dirname(__FILE__) . "/../..");

require_once ROOT_DIR . '/env.php';

// Include Composer first
require_once ROOT_DIR . '/vendor/autoload.php';

function deleteDir($dirPath)
{
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

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

header("Location: /admin/auth/login");

deleteDir("../setup");

die();
