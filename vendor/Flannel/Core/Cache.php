<?php

namespace Flannel\Core;

\Flannel\Core\Config::required(['cache.handler','cache.savepath']);

class Cache {

    protected static $_conn;

    protected static function _getHandler() {
        if (empty(self::$_conn)) {
            $handler = \Flannel\Core\Config::get('cache.handler');
            $savepath = \Flannel\Core\Config::get('cache.savepath');
        
            switch($handler) {
                case 'Redis':
                    self::$_conn = new \Flannel\Core\Cache\Oredis($savepath);
                    break;
                case 'File':
                default:
                    self::$_conn = new \Flannel\Core\Cache\File($savepath);
                    break;
            }
        }
        return self::$_conn;
    }

    public static function get($key) {
        return static::_getHandler()->get($key);
    }

    public static function set($key, $val) {
        return static::_getHandler()->set($key, $val);
    }

    public static function delete($key) {
        return static::_getHandler()->delete($key);
    }
}
