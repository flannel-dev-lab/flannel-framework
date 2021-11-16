<?php

namespace Flannel\Core;

\Flannel\Core\Config::required(['avidbase.account_id']);

class Auth
{

    public static $user;
    public static $permissions;

    private static $cookieAuth = "admin_";

    public static function validateLogin($email, $password)
    {
        self::$cookieAuth .= \Flannel\Core\Config::get('avidbase.account_id');

        $avidBaseClient = new \AvidBase\Client(Config::get('avidbase.account_id'), Config::get('avidbase.api_key'), Config::get('avidbase.is_production'));
        $response = $avidBaseClient->Login($email, $password);

        $accessToken = $response[0];
        if ($accessToken == "" || empty($response[1]['user']) || empty($response[1]['user']['id'])) {
            return 'Unable to login, invalid credentials';
        }

        $userInfo = $response[1];
        $userInfo['access_key'] = bin2hex(random_bytes(32));

        \Flannel\Core\Session::set($userInfo['user']['id'], $userInfo, \Helper_Session::SESSION_NAMESPACE);

        $accessToken = JWT::encode([
            'u' => $userInfo['user']['id'],
            'c' => time(),
            'ak' => $userInfo['access_key'],
        ]);

        // Set the access token
        Cookie::set(self::$cookieAuth, $accessToken);

        return true;

    }
}
