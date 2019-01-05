<?php

namespace Flannel\Core;

class BaseObject implements \ArrayAccess {

    /**
     * @var mixed[]
     */
    private $_data = [];

    /**
    * @var mixed[]
    */
    protected $_origData;

    /**
     * @var string
     */
    protected $_idFieldName = null;

    /**
     * @var string[]
     */
    protected static $_snakeCaseCache = [];

    /**
     * @var mixed[]
     */
    public function __construct($data=[]) {
        $this->initData($data);
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args) {
        $key = $this->_toSnakeCase(substr($method,3));
        switch(substr($method, 0, 3)) {
            case 'get' :
                return $this->getData($key);
            case 'set' :
                return $this->setData($key, ($args[0] ?? null));
            case 'uns' :
                return $this->unsetData($key);
            case 'has' :
                return $this->hasData($key);
        }
        throw new Exception('Call to undefined method '.get_class($this).'::'.$method);
    }

    /**
     * @param mixed $data
     * @return self
     */
    public function initData($data) {
        $this->_origData = $data;
        $this->_data = $data;
        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getOrigData($key) {
        return $this->_origData[$key] ?? null;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->getData($this->_idFieldName ?: 'id');
    }

    /**
     * @param mixed $val
     * @return self
     */
    public function setId($val) {
        $this->setData($this->_idFieldName ?: 'id', $val);
        return $this;
    }

    /**
     * @param array $data
     * @return self
     */
    public function addData($data) {
        foreach($data as $key=>$val) {
            $this->setData($key, $val);
        }
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setData($key, $value) {
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return self
     */
    public function unsetData($key=null) {
        unset($this->_data[$key]);
        return $this;
    }

    /**
     * @return self
     */
    public function unsetAllData() {
        $this->_data = [];
        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getData($key) {
        return $this->_data[$key] ?? null;
    }

    /**
     * @return self
     */
    public function getAllData() {
        return $this->_data;
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function hasData($key) {
        return array_key_exists($key, $this->_data);
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function hasDataChanged($key) {
        return $this->getData($key) != $this->getOrigData($key);
    }

    /**
     * @return mixed[]
     */
    public function getChangedData() {
        $changedData = [];
        $keys = array_keys($this->_data + $this->_origData);

        foreach($keys as $key) {
            if($this->hasDataChanged($key)) {
                $changedData[$key] = $this->getData($key);
            }
        }

        return $changedData;
    }

    /**
     * Eg: FirstName => first_name
     *
     * @param string $str
     * @return string
     */
    protected function _toSnakeCase($str) {
        if(!$str) {
            return $str;
        }
        if(isset(self::$_snakeCaseCache[$str])) {
            return self::$_snakeCaseCache[$str];
        }
        $str[0] = strtolower($str[0]);
        self::$_snakeCaseCache[$str] = preg_replace_callback('/([A-Z])/', function($match){ return '_'.strtolower($match[1]); }, $str);
        return self::$_snakeCaseCache[$str];
    }

    /**
     * Eg: first_name => FirstName
     *
     * @param string $str
     * @return string
     */
    protected function _toCamelCase($str) {
        return str_replace('_', '', ucwords($str, '_'));
    }

    /**
     * @return mixed[]
     */
    public function __debugInfo() {
        return $this->getAllData();
    }

    /**
     * Needed for ArrayAccess
     *
     * @deprecated
     * @see https://php.net/manual/en/class.arrayaccess.php
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        \Flannel\Core\Monolog::get()->warning("Accessing Flannel\Core\BaseObject as Array is deprecated\n" . (new Exception())->getTraceAsString());
        $this->setData($offset, $value);
    }

    /**
     * Needed for ArrayAccess
     *
     * @deprecated
     * @see https://php.net/manual/en/class.arrayaccess.php
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        \Flannel\Core\Monolog::get()->warning("Accessing Flannel\Core\BaseObject as Array is deprecated\n" . (new Exception())->getTraceAsString());
        return $this->hasData($offset);
    }

    /**
     * Needed for ArrayAccess
     *
     * @deprecated
     * @see https://php.net/manual/en/class.arrayaccess.php
     *
     * @param string $offset
     */
    public function offsetUnset($offset) {
        \Flannel\Core\Monolog::get()->warning("Accessing Flannel\Core\BaseObject as Array is deprecated\n" . (new Exception())->getTraceAsString());
        $this->unsetData($offset);
    }

    /**
     * Needed for ArrayAccess
     *
     * @deprecated
     * @see https://php.net/manual/en/class.arrayaccess.php
     * 
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        \Flannel\Core\Monolog::get()->warning("Accessing Flannel\Core\BaseObject as Array is deprecated\n" . (new Exception())->getTraceAsString());
        return $this->getData($offset);
    }

}
