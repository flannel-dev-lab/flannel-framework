<?php

\Flannel\Core\Config::set([
    'env' => [
        'base_urls' => [
            'app' => 'framework.flanneldevlab.com',
        ],
        'mode' => 'api', // Options: website, api
        'developer_mode' => true,
        'error' => [
            'display'   => true, 
            'reporting' => E_ALL,
        ],
    ],
    'db' => [
        'app' => [
            'host'      => '', 
            'dbname'    => '', 
            'user'      => '', 
            'password'  => '', 
            'debug'     => false
        ],
        'readonly' => [
            'host'      => '', 
            'dbname'    => '', 
            'user'      => '', 
            'password'  => '', 
            'debug'     => false
        ],
        'writeonly' => [
            'host'      => '', 
            'dbname'    => '', 
            'user'      => '', 
            'password'  => '', 
            'debug'     => false
        ],
    ],
    'contact' => [
        'email' => [
            'from'      => '',
            'support'   => '',
            'sales'     => ''
        ],
    ],
    'api' => [
        'username' => 'changeme',
        'password' => 'changeme'
    ],
    'cache' => [
        'handler'   => 'Redis',
        'savepath'  => 'tcp://127.0.0.1:6379'
    ],
    'session' => [
        'handler'   => 'redis',
        'savepath'  => 'tcp://127.0.0.1:6379'
    ],
    'alerts' => [
        'file'  => true,
        'email' => null,
        'slack' => '',
    ],
    'sparkpost.api' => [
        'key' => ''
    ],
    'mailchimp.api' => [
        'key' => ''
    ],
    'jwt.key' => '',
    'twilio' => [
        'sid'       => '',
        'token'     => '',
        'number'    => '',
    ],
    'postman' => [
        'enabled' => false,
    ],
    'sentry' => [
        'enabled'     => false,
        'url'         => '',
        'environment' => 'false',
    ],
]);
