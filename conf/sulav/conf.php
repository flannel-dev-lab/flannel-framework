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
      'host' => 'docker.for.mac.host.internal',
      'dbname' => 'sulav',
      'user' => 'root',
      'password' => 'hello123',
      'debug' => false,
    ],
  ],
  'cache' =>
  [
    'handler' => 'redis',
    'savepath' => 'tcp://host.docker.internal:6379',
  ],
  'session' =>
  [
    'handler' => 'redis',
    'savepath' => 'tcp://host.docker.internal:6379',
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
    'is_production' => false,
  ],
]);