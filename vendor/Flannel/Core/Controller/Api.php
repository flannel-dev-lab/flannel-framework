<?php

namespace Flannel\Core\Controller;

\Flannel\Core\Config::required(['env.https.hsts_maxage', 'env.base_url', 'api.username', 'api.password']);

/**
 * Public methods names essentially match the HTTP request method (with the exception of "index"):
 *   head:   Retrieve just HTTP header info (included in this abstract)
 *   index:  Retrieve a collection of items (called when method is "get" and no ID is provided)
 *   get:    Retrieve one item
 *   post:   Create a new item
 *   put:    Update an item
 *   delete: Delete an item
 */
class Api extends \Flannel\Core\Controller {
    
    /**
     * @var mixed[]
     */
    protected $_payload = [];

    /**
     * @var string[]
     */
    protected $_errors = [];

    /**
     * Pre-dispatch
     */
    public function __construct() {
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');

        // enforce HTTPS
        if(\Flannel\Core\Config::get('env.https.enforce')) {
            header('Strict-Transport-Security: max-age=' . (int)\Flannel\Core\Config::get('env.https.hsts_maxage'));
            if(!IS_HTTPS) {
                $this->redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            }
        }

        $this->_authenticate();

        parent::__construct();
    }

    public function _authenticate() {
        // Allow open APIs through
        if ($this->_isPublic) {
            return;
        }

        if ($accessToken = \Flannel\Core\Input::server('HTTP_ACCESS_TOKEN')) {
            try {
                $data = \Flannel\Core\JWT::decode($accessToken);
                $user = (new \Model_User())->load($data->id, 'uuid');

                // If the access key doesn't match, block the user
                if ($data->ak != $user->getAccessKey()) {
                    $this->unauthorized();
                }

                // Block access if the status isn't enabled
                if ($user->getStatus() != \Model_User::STATUS_ENABLED) {
                    $this->unauthorized();
                }

                // Block access if the failed login attempts are too high
                if ($user->getFailedLoginAttempts() >= 6) {
                    $this->unauthorized();
                }

                // All checks have passed, let the user in
                $this->_user = $user;

                // Add the access token to the header
                header("Access-Token: $accessToken");

                // Force the return
                return true;
            } catch (Exception $e) {
                $this->unauthorized();
            }
        }

        // Default to block access
        $this->unauthorized();
    }

    /**
     * Can be issued against any resource to get just the HTTP header info
     */
    public function head() {
        return;
    }

    /**
     * @param mixed $data
     * @param string $errMsg
     * @param int $errCode
     */
    protected function _send() {
        $this->outputJson($this->_payload);
    }

    /**
     * @param mixed $item
     */
    protected function _sendError($code, $devMessage = '', $moreInfo = '') {
        header('HTTP/1.1 422 Unprocessable Entity');
        $this->outputJson([
            'code'          => $code,
            'dev_message'   => $devMessage,
            'more_info'     => $moreInfo,
        ]);
    }

    /**
     * @param mixed $item
     */
    protected function _sendItem($item) {
        $this->_payload = $this->_translate($item);
        $this->_send();
    }

    /**
     * @param \Flannel\Core\BaseObject[] $collection
     */
    protected function _sendCollection($collection) {
        $data = [];
        foreach($collection as $item) {
            $data[] = $this->_translate($item);
        }

        $this->_payload = $data;
        
        $this->_send();
    }

    /**
     * Extended this method to standardize the output.
     * Even if the underlying data model changes, output should remain the same
     *
     * @param \Flannel\Core\BaseObject $item
     * @return mixed[]
     */
    protected function _translate($item) {
        return null;
    }

}
