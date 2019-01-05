<?php

namespace Flannel\Core;

/**
 * Autoloader
 *
 * This is a modified PSR-4 autoloader. The exceptions are:
 * - top-level namespace is not required
 * - underscores have the same means as a backslash
 *
 * This class is loosely based on the PHP-FIG example at:
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md#class-example
 */
class Autoloader {

    /**
     * @var string[]
     */
    protected static $_paths = [];

    /**
     *
     */
    public static function register() {
        spl_autoload_register(array(__CLASS__, 'loadClass'));
    }

    /**
     * @param string $baseDir
     * @param string $prefix
     * @param bool $prepend
     */
    public static function addPath($baseDir, $prefix='', $prepend=false) {
        $baseDir = rtrim($baseDir, DS) . DS;

        if($prefix) {
            $prefix = trim($prefix, DS) . DS;
        }

        if(!isset(static::$_paths[$prefix])) {
            static::$_paths[$prefix] = [];
        }

        if($prepend) {
            array_unshift(static::$_paths[$prefix], $baseDir);
        } else {
            array_push(static::$_paths[$prefix], $baseDir);
        }
    }

    /**
     * Loads the class file for a given class name.
     *
     * @param string $class
     * @return string|false
     */
    public static function loadClass($class) {
        $prefix = $class;


        // work backwards through the namespace names of the fully-qualified class name
        while (false !== $pos = strrpos($prefix, DS)) {

            $prefix = substr($class, 0, $pos + 1);
            $relativeClass = substr($class, $pos + 1);

        
            $mapped_file = static::loadMappedFile($relativeClass, $prefix);
            if($mapped_file) {
                return $mapped_file;
            }

            // remove the trailing namespace separator for the next iteration
            $prefix = rtrim($prefix, DS);
        }

        $mapped_file = static::loadMappedFile($class);
        if($mapped_file) {
            return $mapped_file;
        }

        return false;
    }

    /**
     * @param string $relativeClass
     * @param string $prefix
     * @return bool
     */
    protected static function loadMappedFile($relativeClass, $prefix='') {
        if(isset(static::$_paths[$prefix]) === false) {
            return false;
        }

        $relativeClass = str_replace(['/','\\','_'], DS, $relativeClass);
     
        foreach(static::$_paths[$prefix] as $base_dir) {
            $file = $base_dir . $relativeClass . '.php';
            if(file_exists($file)) {
                require_once $file;
                return true;
            }
        }

        return false;
    }

}
