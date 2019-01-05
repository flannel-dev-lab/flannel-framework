<?php

namespace Flannel\Core\Router;
use \Flannel\Core\Router;

/**
 * Router
 * Converts request path to respective controller
 *
 * Pattern:
 * /module/controller/action[/key/value/key/value/...]
 *
 * The action method will be called on the controller class. Then, the render()
 * method will automatically be called.
 *
 * Controller section can contain underscores which convert to subdirectories
 * in the code base. Class name must contain full name (with underscores).
 * Eg, /mymodule/controller_with_subdirectories/index
 *  -> /controllers/mymodule/controller/with/subdirectories -> controller_with_subdirectories::index
 *
 * All key/value pairs will be added to the Input class (via $_GET). They will
 * replace any duplicate values that may already be set.
 * Eg, /mymodule/index/index/id/1?id=2 -> $_GET[id] will equal 1
 *
 */
class Standard {

    /**
     * Protected constructor
     * Must be called via Router::run();
     *
     * @param string $requestUri
     */
    public function __construct($requestUri) {
        // 0=empty/1=module/2=controller/3=action/4=key/5=value/6=key/7=value/...
        $dirs = explode('/', rtrim($requestUri, '/'));

        $module = $dirs[1] ?? '';
        $controller = $dirs[2] ?? 'index';
        $action = $dirs[3] ?? 'index';
        $variables = array_slice($dirs, 4);

        if(!$module) {
            Router::getDefaultController()->index();
        }

        $this->_parseKeyValuePairs($variables);

        $found = $this->_call($module, $controller, $action);
        if(!$found) {
            Router::getDefaultController()->notFound();
        }
    }

    /**
     * Convert a flat array of URL parts to key value pairs of variables
     *
     * @param string[] $vars
     * @return self
     */
    protected function _parseKeyValuePairs($vars) {
        while(!empty($vars)) {
            $key = array_shift($vars);
            $value = array_shift($vars) ?: '';
            $_GET[$key] = $value;
        }
        return $this;
    }

    /**
     * Load the controller and call the action
     *
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    protected function _call($module, $controller, $action) {
        $basePath = realpath(\Flannel\Core\Config::get('dir.controller'));
        $module = str_replace('-', '', $module);
        $controller = str_replace('-', '', $controller);
        $action = str_replace('-', '', $action);

        $filepath = realpath($basePath . '/' . ucwords($module . '/' . str_replace('_','/',$controller), '/') . '.php');
        
        //ensure $filepath is still within $basePath
        if($filepath && strpos($filepath, $basePath)===0) {
            try {
                $className = 'Controller_' . ucwords($module . '_' . $controller, '_');
                $controller = new $className();
                if(is_callable(array($controller, $action))) {
                    $controller->$action();
                    $controller->render();
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
