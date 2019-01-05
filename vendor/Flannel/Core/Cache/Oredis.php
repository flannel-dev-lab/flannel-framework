<?php

namespace Flannel\Core\Cache;

require_once ('/usr/share/pear/Predis/Autoloader.php');
\Predis\Autoloader::register();

class Oredis {

    protected $_redis;

    public function __construct($savepath) {
        $this->_redis = new \Predis\Client([
            'scheme'    => parse_url($savepath, PHP_URL_SCHEME),
            'host'      => parse_url($savepath, PHP_URL_HOST),
            'port'      => parse_url($savepath, PHP_URL_PORT),
        ]);
    }

    public function set($key, $val) {
        return $this->_redis->set($key, json_encode($val));
    }

    public function get($key) {
        $val = $this->_redis->get($key);
        return json_decode($val, true);
    }

    public function delete($key) {
        return $this->_redis->del($key);
    }
    
}
