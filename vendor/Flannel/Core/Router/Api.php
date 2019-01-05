<?php

namespace Flannel\Core\Router;
use \Flannel\Core\Router;

/**
 * Router
 * Converts request path to respective controller
 *
 * Pattern:
 * /api/version/resource[/resourceId[/subresource/subresourceId]]
 *
 * Public methods names essentially match the HTTP request method (with the exception of "index"):
 *   head:   Retrieve just HTTP header info (included in this abstract)
 *   index:  Retrieve a collection of items (called when method is "get" and no ID is provided)
 *   get:    Retrieve one item
 *   post:   Create a new item
 *   put:    Update an item
 *   delete: Delete an item
 *
 * The resourceId and subresourceId will be added to the Input class (via $_GET). They will
 * replace any duplicate values that may already be set.
 * Eg, /api/v2/users/1?resourceId=2 -> $_GET[resourceId] will equal 1
 *
 */
class Api {

    /**
     * Protected constructor
     * Must be called via Router::run();
     *
     * @param string $requestUri
     */
    public function __construct($requestUri) {

        // 0=empty/1=version/2=resource/3=resourceId/4=subresource/5=subresourceId
        $dirs = explode('/', $requestUri);

        $version = $dirs[1] ?? null;
        $resource = $dirs[2] ?? null;
        $resourceId = $dirs[3] ?? null;
        $subresource = $dirs[4] ?? null;
        $subresourceId = $dirs[5] ?? null;
        $method = strtolower(\Flannel\Core\Input::server('REQUEST_METHOD'));

        if($resourceId) {
            $_GET['resourceId'] = $resourceId;
        }
        if($subresourceId) {
            $_GET['subresourceId'] = $subresourceId;
        }

        if($method==='get') {
            if(!$resourceId || (!$subresourceId && $subresource)) {
                $method = 'list';
            }
        }

        // Support for custom resource methods
        if (strpos($resource, ':') !== false) {
            list($resource, $method) = explode(':', $resource);
        }

        // Support for custom subresource methods
        if (strpos($subresource, ':') !== false) {
            list($subresource, $method) = explode(':', $subresource);
        }

        $found = $this->_call($method, $version, $resource, $subresource);

        if(!$found) {
            \Flannel\Core\Router::getDefaultController()->notFound();
        }
    }

    /**
     *
     * @param string $method
     * @param string $version
     * @param string $resource
     * @param string $subresource
     * @return boolean
     */
    protected function _call($method, $version, $resource, $subresource=null) {
        $basePath = realpath(\Flannel\Core\Config::get('dir.controller'));
        $relativePath = $version . '/' . str_replace('-','/',$resource) . ($subresource ? '/'.str_replace('-','/',$subresource) : '');

        $filepath = realpath($basePath . '/' . ucwords($relativePath, '/') . '.php');

        //ensure $filepath is still within $basePath
        if($filepath && strpos($filepath, $basePath)===0) {

            try {
                $className = 'Controller_' . str_replace('/','_',ucwords($relativePath,'/'));
                $controller = new $className();
                
                if(is_callable(array($controller, $method))) {
                    $controller->$method();

                    return true;
                } else {
                    $controller->methodNotAllowed();
                    return true;
                }
            } catch(Exception $e) {
                \Flannel\Core\Monolog::get()->debug($e->getMessage());
                return false;
            }
        }

        return false;
    }

}
