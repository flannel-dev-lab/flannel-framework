<?php

class Controller_Default extends \Flannel\Core\Controller {

    /**
     * @var string
     */
    static public $template;

    /**
     * @var bool
     */
    protected $_isPublic = true;



    /**
     * Pre-dispatch
     */
    public function __construct() {
        header('X-Frame-Options: deny');
        header('X-XSS-Protection: 1; mode=block');

        parent::__construct();

	    if(!self::$template) {
            self::$template = new \stdClass();
        }
    }

    /**
     * Route user to log in or to the dashboard
     */
    public function index() {
   
    }

    /**
     * Validate CSRF token
     *
     * @param string|null $token
     * @param bool $hardStopOnFailure
     * @return bool (or exit)
     */
    protected function _validateCSRF($token=null, $hardStopOnFailure=true) {
        if(Flannel\Core\CSRF::validate($token)) {
            return true;
        }
        if($hardStopOnFailure) {
            $this->forbidden();
        }
        return false;
    }

    /**
     * Render templates
     */
    public static final function render() {
        if(!empty(self::$template)) {
            foreach(self::$template as $view) {
                echo $view->render();
            }
        }
    }

    /**
     * @return $this
     */
    protected function _startSession() {
        Helper_Session::start();
        return $this;
    }

    /**
     * Deal with invalidated sessions
     */
    protected function _invalidSessionRedirect() {
    
    }

}
