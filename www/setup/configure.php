<?php

// $_POST['config_file'] . "/conf.php"
$templateContent = file_get_contents("../../conf/conf.php.template");
$templateContent = str_replace('YOUR_USERNAME', $_POST['config_file'], $templateContent);

if (!file_put_contents("../../conf/conf.php", $templateContent)) {
    echo "Failed to write to a config file.";
    die();
}

header("Location: /admin/auth/login");
die();
