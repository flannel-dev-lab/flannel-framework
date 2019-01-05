<?php

namespace Flannel\Core;

class Input {

    protected static $_isHydrated = false;
    protected static $_phpInput = [];

    /**
     * $_GET must be used since \Flannel\Core\Router\* may add vars from the URL
     */
    static public function get($key, $filter=FILTER_SANITIZE_STRING, $options=FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH) {
        return isset($_GET[$key]) ? filter_var($_GET[$key], $filter, $options) : null;
    }

    /**
     * $_POST must be used to allow overriding input for local instantiation of API classes
     */
    static public function post($key, $filter=FILTER_SANITIZE_STRING, $options=FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH) {
        //return filter_input(INPUT_POST, $key, $filter, $options);
        return isset($_POST[$key]) ? filter_var($_POST[$key], $filter, $options) : null;
    }

    static public function postArray($key) {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    static public function cookie($key, $filter=FILTER_SANITIZE_STRING, $options=FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH) {
        return filter_input(INPUT_COOKIE, $key, $filter, $options);
    }

    public static function put($key, $filter=FILTER_SANITIZE_STRING, $options=FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH) {
        static::_hydrate();
        return isset(static::$_phpInput[$key]) ? filter_var(static::$_phpInput[$key], $filter, $options) : null;
    }

    public static function find($key, $filter=FILTER_SANITIZE_STRING, $options=FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH) {
        static::_hydrate();
        $val = isset(static::$_phpInput[$key]) ? filter_var(static::$_phpInput[$key], $filter, $options) : null;
   
        // Check if there is an array posted 
        if (empty($val)) {
            $val = self::postArray($key);
        }

        return $val;
    }

    public static function debug() {
        return static::$_phpInput;
    }
    
    /**
     * $_SERVER must be used since some things are added at runtime (eg, PHP_AUTH_USER and PHP_AUTH_PW)
     */
    public static function server($key, $filter=FILTER_SANITIZE_STRING, $options=FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH) {
        return isset($_SERVER[$key]) ? filter_var($_SERVER[$key], $filter, $options) : null;
    }

    static public function env($key, $filter=FILTER_SANITIZE_STRING, $options=FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH) {
        return filter_input(INPUT_ENV, $key, $filter, $options);
    }

    /**
     * This is the least preferable
     */
    static public function request($key, $filter=FILTER_SANITIZE_STRING, $options=FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH) {
        return isset($_REQUEST[$key]) ? filter_var($_REQUEST[$key], $filter, $options) : null;
    }

    protected static function _hydrate() {
        if (static::$_isHydrated) {
            return;
        }

        static::$_phpInput = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() != JSON_ERROR_NONE) {
            parse_str(urldecode(file_get_contents('php://input')), static::$_phpInput);
        }

        static::$_isHydrated = true;

        return true;
    }
    
}
