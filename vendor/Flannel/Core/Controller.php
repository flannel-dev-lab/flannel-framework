<?php

namespace Flannel\Core;

class Controller {

    /**
     * Pre-dispatch
     */
    public function __construct() {
    }

    /**
     * Output as JSON
     * 
     * @param string|mixed[] $data
     */
    public function outputJson($data) {
        header('Content-Type: application/json');
        echo is_array($data)
            ? json_encode($data, (\Flannel\Core\Config::get('env.developer_mode') ? JSON_PRETTY_PRINT : 0))
            : $data;
        exit;
    }

    /**
     * HTTP 204 No Content
     */
    public function noContent() {
        header('HTTP/1.1 204 No Content');
        exit;
    }

    /**
     * HTTP 302 Found
     */
    public function redirect($location) {
        header('HTTP/1.1 302 Found');
        header('Location: ' . $location);
        exit;
    }

    /**
     * HTTP 401 Unauthorized
     */
    public function unauthorized() {
        header('WWW-Authenticate: Basic realm="Unauthorized"');
        header('HTTP/1.1 401 Unauthorized');
        exit;
    }

    /**
     * HTTP 403 Forbidden
     */
    public function forbidden() {
        header('HTTP/1.1 403 Forbidden');
        exit;
    }

    /**
     * HTTP 404 Not Found
     */
    public function notFound() {
        header('HTTP/1.1 404 Not Found');
        exit;
    }

    /**
     * HTTP 503 Service Unavailable
     */
    public function methodNotAllowed() {
        header('HTTP/1.1 405 Method Not Allowed');
        exit;
    }

    /**
     * HTTP 503 Service Unavailable
     */
    public function serviceUnavailable() {
        header('HTTP/1.1 503 Service Unavailable');
        exit;
    }

}
