<?php \Flannel\Core\Config::set([
  'env' => 
  [
    'base_urls' => 
    [
      'app' => 'http://localhost:5001',
    ],
    'mode' => 'website',
    'developer_mode' => false,
    'error' => 
    [
      'display' => true,
      'reporting' => 32767,
    ],
  ],
  'db' => 
  [
    'app' => 
    [
      'host' => 'localhost',
      'dbname' => 'sulav',
      'user' => 'root',
      'password' => '',
      'debug' => false,
    ],
  ],
  'cache' => 
  [
    'handler' => 'file',
    'savepath' => '/temp/logs/default.log',
  ],
  'session' => 
  [
    'handler' => 'file',
    'savepath' => '/temp/logs/default.log',
  ],
  'sentry' => 
  [
    'enabled' => false,
    'url' => '',
    'environment' => '',
  ],
  'jwt.key' => '',
  'avidbase' => 
  [
    'account_id' => '3d40e9a9-2832-4802-a351-290220da4b5a',
    'api_key' => 'JY9t1FadgBZz5avY0SYAIPci8fwIkNpNAkWbzK4shJAALI5MrrTEN7dbSfwej5Mt',
    'is_production' => true,
  ],
]);