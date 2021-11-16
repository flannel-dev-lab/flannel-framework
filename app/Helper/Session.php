<?php

class Helper_Session extends \Flannel\Core\Session
{

    const SESSION_NAMESPACE = 'AUTH';

    protected static $_user;
    protected static $_permissions;
    protected static $_accountId;
    protected static $_accessToken = '';
    protected static $_isLoggedIn = false;
    protected static $_cookieAuth = 'admin_';

    public static function Init()
    {
        self::$_accountId = \Flannel\Core\Config::get('avidbase.account_id');
        self::$_cookieAuth .= self::$_accountId;
        parent::start();
        self::_inflateUser();
    }

    public static function Destroy()
    {
        parent::destroy();
    }

    protected static function _inflateUser()
    {
        // The cookie doesn't exist
        if (!self::$_accessToken = \Flannel\Core\Cookie::get(self::$_cookieAuth)) {
            self::$_isLoggedIn = false;
            self::$_accessToken = '';
            return;
        }

        // Decode the access token
        $data = \Flannel\Core\JWT::decode(self::$_accessToken);

        $userInfo = \Flannel\Core\Session::get($data->u, self::SESSION_NAMESPACE);
        // The user info doesn't exist
        if (empty($userInfo)) {
            self::$_isLoggedIn = false;
            self::$_accessToken = '';
            return;
        }

        if (!$userInfo->user['id'] || $userInfo->access_key != $data->ak) {
            self::$_isLoggedIn = false;
            self::$_accessToken = '';
            return;
        }

        $userData = new \Flannel\Core\BaseObject($userInfo->user['data']);
        $user = $userInfo->user;
        unset($user['data']);
        $user['user_data'] = $userData;

        // Set the basic credentials for an authed user
        self::$_user = new \Flannel\Core\BaseObject($user);
        self::$_permissions = new \Flannel\Core\BaseObject($userInfo->permissions);
        self::$_isLoggedIn = true;
    }

    public static function IsLoggedIn(): bool
    {
        return self::$_isLoggedIn;
    }

    public static function GetUser(): \Flannel\Core\BaseObject
    {
        return self::$_user;
    }

    public static function GetAccountId(): string
    {
        return self::$_accountId;
    }

    public static function AssertPerm($object, $operation): bool
    {
        if (isset($_permissions["$object|$operation"])) {
            return true;
        }

        return false;
    }
}