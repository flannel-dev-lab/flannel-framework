<?php

namespace Flannel\Core\Cache;

class File {

    protected $_savepath;

    public function __construct($savepath) {
        $this->_savepath = $savepath;
    }

    protected function _getFilename($key) {
        $hash = sha1($key);
        return $this->_savepath . DS . $hash[0] . $hash[1] . DS . $hash;
    }

    public function set($key, $val, $flag = false, $expire = 3600) {
        $file = $this->_getFilename($key);
        @mkdir(dirname($file));
        return (bool)file_put_contents($file, serialize($val), LOCK_EX);
    }

    public function get($key) {
        $val = @file_get_contents($this->_getFilename($key), false);
        return $val ? unserialize($val) : null;
    }

    public function delete($key) {
        return @unlink($this->_getFilename($key));
    }

}
