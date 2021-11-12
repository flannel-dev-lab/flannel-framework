<?php

define('ROOT_DIR', dirname(__FILE__) . "/../..");

require_once ROOT_DIR . '/env.php';

// Include Composer first
require_once ROOT_DIR . '/vendor/autoload.php';

$config = [
    'env' => [
        'base_urls' => [
            'app' => $_POST['base_url'],
        ],
        'mode' => $_POST['mode'],
        'developer_mode' => false,
        'error' => [
            'display' => true,
            'reporting' => E_ALL,
        ],
    ],
    'db' => [
        'app' => [
            'host' => $_POST['mysql_host'],
            'dbname' => $_POST['mysql_database'],
            'user' => $_POST['mysql_username'],
            'password' => $_POST['mysql_password'],
            'debug' => false,
        ],
    ],
    'cache' => [
        'handler' => $_POST['caching_type'],
        'savepath' => $_POST['caching_save_path'],
    ],
    'session' => [
        'handler' => $_POST['caching_type'],
        'savepath' => $_POST['caching_save_path'],
    ],
    'sentry' => [
        'enabled' => $_POST['enable_sentry'] == 'true',
        'url' => $_POST['sentry_url'],
        'environment' => $_POST['sentry_environment'],
    ],
    'jwt.key' => '',
    'avidbase' => [
        'account_id' => $_POST['admin_account_id'],
        'api_key' => $_POST['admin_api_key'],
        'is_production' => true,
    ],
];

$mysqli = new \mysqli($config['db']['app']['host'], $config['db']['app']['user'], $config['db']['app']['password'], $config['db']['app']['dname']);
// Check connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    die();
}
$mysqli->close();

$cacheStoreSuccess = true;
switch ($_POST['caching_type']) {
    case "file":
        if ($Handle = fopen("../.." . $_POST['caching_save_path'], 'w')) {
            if (!fwrite($Handle, "testing file write access by the installer script.")) {
                $cacheStoreSuccess = false;
            }
            fclose($Handle);
        } else {
            $cacheStoreSuccess = false;
        }
        break;

    case "redis":
        $clientRedis = new Redis();
        try {
            $connected = $clientRedis->connect($_POST['caching_save_path']);
            if (!$connected) {
                $cacheStoreSuccess = false;
            }
        } catch (Exception $e) {
            $cacheStoreSuccess = false;
        }
        break;

    default:
        $cacheStoreSuccess = false;
        break;
}
if (!$cacheStoreSuccess) {
    echo "Failed to connect to the caching source.";
    die();
}

$avidBaseClient = new \AvidBase\Client($_POST['admin_account_id'], $_POST['admin_api_key'], false);
$users = $avidBaseClient->FindUser($_POST['admin_email']);
if (empty($users)) {
    $user = new \AvidBase\User();
    $user->Email = $_POST['admin_email'];
    $user->Password = $_POST['admin_password'];
    $response = $avidBaseClient->CreateUser($user);
    if (!$response) {
        echo "Failed to establish AvidBase connection and create an admin user.";
        die();
    }
}

$configString = var_export($config, true);
$configString = str_replace("array (", "[", $configString);
$configString = str_replace(")", "]", $configString);
if (!file_exists("../../conf/" . $_POST['config_name'])) {
    mkdir("../../conf/" . $_POST['config_name'], 0777, true);
}
if (!file_put_contents("../../conf/" . $_POST['config_name'] . "/conf.php", "<?php \Flannel\Core\Config::set(" . $configString . ");")) {
    echo "Failed to write to a config file.";
    die();
}

header("Location: config_setup.php");
die();
