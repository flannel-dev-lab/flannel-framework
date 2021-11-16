<?php

class Controller_Default extends \Flannel\Core\Controller
{

    /**
     * @var string
     */
    static public $template;

    /**
     * @var bool
     */
    protected $_isPublic = false;


    /**
     * Pre-dispatch
     */
    public function __construct()
    {
        header('X-Frame-Options: deny');
        header('X-XSS-Protection: 1; mode=block');

        parent::__construct();

        $sessionFailure = true;
        try {
            Helper_Session::Init();
            $sessionFailure = false;
        } catch (Exception $e) {
            // TODO: maybe route this to Sentry, might be too much noise though
        }

        // Redirect any any non-authed, non-public requests
        if (!$this->_isPublic && ($sessionFailure || !Helper_Session::isLoggedIn())) {
            $this->redirect("/admin/auth/login");
        }

        if (!self::$template) {
            self::$template = new \stdClass();
        }
    }

    /**
     * Route user to log in or to the dashboard
     */
    public function index()
    {
        $this->redirect(Helper_Session::isLoggedIn() ? '/admin' : '/admin/auth/login');
    }

    /**
     * Validate CSRF token
     *
     * @param string|null $token
     * @param bool $hardStopOnFailure
     * @return bool (or exit)
     */
    protected function _validateCSRF($token = null, $hardStopOnFailure = true)
    {
        if (Flannel\Core\CSRF::validate($token)) {
            return true;
        }
        if ($hardStopOnFailure) {
            $this->forbidden();
        }
        return false;
    }

    /**
     * Render templates
     */
    public static final function render()
    {
        if (!empty(self::$template)) {
            foreach (self::$template as $view) {
                echo $view->render();
            }
        }
    }

    /**
     * Deal with invalidated sessions
     */
    protected function _invalidSessionRedirect()
    {

    }

}
