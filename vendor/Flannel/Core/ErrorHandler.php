<?php

namespace Flannel\Core;

class ErrorHandler {

    protected static $_codes = [
        E_ERROR => 'Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse Error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core Error',
        E_CORE_WARNING => 'Core Warning',
        E_COMPILE_ERROR => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_STRICT => 'Strict Notice',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        E_DEPRECATED => 'Deprecated functionality'
    ];

    /**
     * Init
     */
    public static function init() {
        ini_set('display_errors', \Flannel\Core\Config::get('env.error.display'));
        error_reporting(\Flannel\Core\Config::get('env.error.reporting'));
    }

    /**
     * Try to log fatal errors using register_shutdown_function
     */
    public static function shutdown() {
        $err = error_get_last();
        $type = $err['type'] ?? null;
        $msg  = $err['message'] ?? null;
        $file = $err['file'] ?? null;
        $line = $err['line'] ?? null;

        if($err) {
            self::log($type, $msg, $file, $line);
        }
    }

    /**
     * Try to log errors (warnings, notices, etc) using set_error_handler
     *
     * It is important to remember that the standard PHP error handler is completely
     * bypassed for the error types specified by error_types unless the callback
     * function returns FALSE.
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return bool
     */
    public static function log($errno, $errstr, $errfile, $errline) {
        if (strpos($errstr, 'DateTimeZone::__construct')!==false) {
            // there's no way to distinguish between caught system exceptions and warnings
            return false;
        }

        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            return false;
        }

        $errorMessage =
            (isset(self::$_codes[$errno]) ? self::$_codes[$errno] : "Unknown error ($errno)")
            . ": {$errstr} in {$errfile} on line {$errline}";

        switch($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
                \Flannel\Core\Monolog::get()->error($errorMessage);
                break;
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                \Flannel\Core\Monolog::get()->warning($errorMessage);
            case E_NOTICE:
            case E_USER_NOTICE:
            case E_STRICT:
            case E_DEPRECATED:
                \Flannel\Core\Monolog::get()->notice($errorMessage);
        }

        return false;
    }

}
