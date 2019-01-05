<?php

namespace Flannel\Core;

class Config {

    /**
     * @var bool
     */
    protected static $_isWritable = true;

    /**
     * @var mixed[]
     */
    protected static $_data = [];

    /**
     * Set value(s)
     *
     * Arrays are recursively flattened with dot notation. Use best form
     * to increase code readability.
     *
     * Flannel\Core\Config::set('mymodule.auth.username', 'joe.smith');
     * Flannel\Core\Config::set('mymodule.auth.password', 'password123');
     * -- OR --
     * Flannel\Core\Config::set('mymodule', [
     *     'auth.username' => 'joe.smith',
     *     'auth.password' => 'password123'
     * ])
     * -- OR --
     * Flannel\Core\Config::set('mymodule', [
     *     'auth' => [
     *         'username' => 'joe.smith',
     *         'password' => 'password123'
     *     ]
     * ])
     * -- OR --
     * Flannel\Core\Config::set([
     *     'mymodule' => [
     *         'auth' => [
     *             'username' => 'joe.smith',
     *             'password' => 'password123'
     *         ]
     *     ]
     * ])
     *
     * THEN
     * Flannel\Core\Config::get('mymodule.auth.username')
     *
     * @param string $namespace
     * @param mixed[] $values
     * @throws Exception
     */
    public static function set($namespace, $values=null) {
        if(!static::$_isWritable) {
            throw new Exception('Config values are set to read-only');
        }
        if(is_array($namespace)) {
            foreach($namespace as $key=>$value) {
                static::set($key, $value);
            }
        } elseif(is_array($values)) {
            foreach($values as $key=>$value) {
                static::set($namespace.'.'.$key, $value);
            }
        } else {
            static::$_data[$namespace] = $values;
        }
    }

    /**
     * Get a single config value
     *
     * Config::get('mymodule.username')
     * 
     * @param string $key
     * @return mixed
     */
    public static function get($key) {
        return static::$_data[$key] ?? null;
    }

    /**
     * Get an array of config values
     *
     * Config::getArray('mymodule.arrayofdata')
     * 
     * @param string $key
     * @return mixed
     */
    public static function getArray($key) {

        $result = [];

        foreach(static::$_data as $k => $v) {

            if(strpos($k, $key) === 0) {

                $subkey = str_replace(sprintf('%1$s.', $key), '', $k);

                if(strpos($subkey, '.') !== false) {
                    $subsubkey          = explode('.', $subkey)[0];
                    $result[$subsubkey] = static::getArray(sprintf('%1$s.%2$s', $key, $subsubkey));
                    continue;
                }

                $result[$subkey] = static::$_data[$k];
            }
        }

        return empty($result) ? null : $result;
    }

    /**
     * Make read-only
     */
    public static function lock() {
        ksort(static::$_data);
        static::$_isWritable = false;
    }

    /**
     * @param string[] $keys
     * @throws Exception
     */
    public static function required($keys) {
        $missingKeys = [];
        foreach($keys as $key){
            if(!array_key_exists($key, static::$_data)) {
                $missingKeys[] = $key;
            }
        }

        if(!empty($missingKeys)) {
            throw new Exception('Config value must be defined for: "' . implode(', ', $missingKeys));
        }
    }

}
