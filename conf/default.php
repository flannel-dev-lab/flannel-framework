<?php

\Flannel\Core\Config::set([
    'env' => [
        'mode' => 'website', // Options: website, api
        'base_url' => null,
        'developer_mode' => false,
        'https' => ['enforce'=>false, 'hsts_maxage'=>SECONDS_PER_DAY*30],
        'error' => ['display'=>false, 'reporting'=>E_ALL]
    ],
    'db' => [
        'app' => ['host'=>null, 'dbname'=>null, 'user'=>null, 'password'=>null, 'debug'=>false],
        'etl' => ['host'=>null, 'dbname'=>null, 'user'=>null, 'password'=>null, 'debug'=>false]
    ],
    'contact' => [
        'email' => [
            'from' => '',
            'support' => '',
            'sales' => ''
        ],
        'phone' => [
            'support' => ''
        ],
        'api' => [
            'username' => null,
            'password' => null
        ]
    ],
    'cache' => [
        'handler' => 'File',
        'savepath' => ROOT_DIR.'/temp/cache'
    ],
    'session' => [
        'handler' => 'files',
        'savepath' => ROOT_DIR.'/temp/sessions'
    ],
    'cookie' => [
        'default.age' => SECONDS_PER_YEAR
    ],
    'dir' => [
        'root'  => ROOT_DIR,
        'log'   => ROOT_DIR.'/temp/logs',
        'www'   => ROOT_DIR.'/www',
        'controller' => ROOT_DIR.'/app/Controller',
        'template' => [
            'email' => ROOT_DIR.'/template/email',
            'view'  => ROOT_DIR.'/template/view',
        ]
    ],
    'alerts' => [
        'file'  => true, //bool
        'email' => null, //address
        'slack' => null, //url
    ],
    'sparkpost.api' => [
        'key' => null
    ],
    'mailchimp.api' => [
        'key' => null
    ],
    'jwt.key' => null
]);

// TODO - 100% the wrong place for this
function keyLengthSort($a, $b) {
    if (strlen($a) >= strlen($b)) {
        return false;
    }

    return true;
}
