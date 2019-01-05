<?php

namespace Flannel\Core;

\Flannel\Core\Config::required(['cookie.default.age','env.developer_mode']);

class Cookie {

    static function set($name, $value, $time = false, $path='/', $domain=null, $secure=false, $httponly=false) {
        if(!$time) { //session cookie
            $expire = 0;
        } elseif($time === true) { //use default
            $expire = time() + \Flannel\Core\Config::get('cookie.default.age');
        } else {
            $expire = time() + (int)$time;
        }

        if($secure && \Flannel\Core\Config::get('env.developer_mode') && !IS_HTTPS) {
            $secure = false;
        }

        return setcookie($name, base64_encode($value), $expire, $path, $domain, $secure, $httponly);
    }

    public static function get($name) {
        if (isset($_COOKIE[$name])) {
            return base64_decode($_COOKIE[$name]);
        }

        return false;
    }

    public static function getAll() {
        return $_COOKIE;
    }

    public static function delete($name, $path='/', $domain=null, $secure=false, $httponly=false) {
        if($secure && \Flannel\Core\Config::get('env.developer_mode') && !IS_HTTPS) {
            $secure = false;
        }
        return setcookie($name, '', time()-SECONDS_PER_DAY, $path, $domain, $secure, $httponly);
    }
    
}
