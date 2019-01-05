<?php

namespace Flannel\Core;

\Flannel\Core\Config::required(['dir.controller']);

class Router {

    /**
     * @var string
     */
    protected static $_defaultControllerName;

    /**
     * @var string
     */
    protected $_domain;

    /**
     * Effectively the public constructor
     */
    public static function run($defaultControllerName) {
        static::$_defaultControllerName = $defaultControllerName;
        
	try {
            if (\Flannel\Core\Config::get('env.mode') == 'api') {
                new Router\Api(static::getRequestedPath());
            } else {
                new Router\Standard(static::getRequestedPath());
            }
        } catch(Exception $e) {
            \Flannel\Core\Monolog::get()->error($e->getMessage());
            static::getDefaultController()->serviceUnavailable();
        }
    }

    /**
     * @return string
     */
    public static function getRequestedUrl() {
        return \Flannel\Core\Input::server('REQUEST_SCHEME', FILTER_SANITIZE_URL, null) . '://'
            . \Flannel\Core\Input::server('HTTP_HOST', FILTER_SANITIZE_URL, null)
            . \Flannel\Core\Input::server('REQUEST_URI', FILTER_SANITIZE_URL, null);
    }

    /**
     * Get the requested host
     * Eg, api.admin.flanneldevlab.com
     *
     * @return string
     */
    public static function getRequestedHost() {
        $url = static::getRequestedUrl();
        return parse_url(static::getRequestedUrl(), PHP_URL_HOST);
    }

    /**
     * Get the requested path
     * Eg, /module/controller/action/key/value
     *
     * @return string
     */
    public static function getRequestedPath() {
        return parse_url(static::getRequestedUrl(), PHP_URL_PATH);
    }

    /**
     * @return \Flannel\Core\Controller
     */
    public static function getDefaultController() {
        return new static::$_defaultControllerName();
    }

}
