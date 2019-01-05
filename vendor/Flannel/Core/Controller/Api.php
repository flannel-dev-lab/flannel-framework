<?php

namespace Flannel\Controller;

\Flannel\Config::required(['env.https.hsts_maxage', 'env.base_url', 'Flannel.api.username', 'Flannel.api.password']);

/**
 * Public methods names essentially match the HTTP request method (with the exception of "index"):
 *   head:   Retrieve just HTTP header info (included in this abstract)
 *   index:  Retrieve a collection of items (called when method is "get" and no ID is provided)
 *   get:    Retrieve one item
 *   post:   Create a new item
 *   put:    Update an item
 *   delete: Delete an item
 */
class Api extends \Flannel\Controller {
    
    /**
     * @var mixed[]
     */
    protected $_payload = [
        'success' => true,
        'errors' => [],
        'result' => null
    ];

    /**
     * @var string[]
     */
    protected $_errors = [];

    /**
     * Pre-dispatch
     */
    public function __construct() {
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        // ensure only accessed from 'env.base_urls'
        $applications = array('app', 'admin', 'loyalty', 'merchant', 'pos');
        $match = false;
        foreach ($applications as $app) {
            $url = \Flannel\Config::get("env.base_urls.$app");
            $host = \Flannel\Router::getRequestedHost();

            if(strpos($host, $url) !== 0) {
                $match = true;
                break;
            }
        }

        if (!$match) {
            $this->forbidden();
        }

        // enforce HTTPS
        if(\Flannel\Config::get('env.https.enforce')) {
            header('Strict-Transport-Security: max-age=' . (int)\Flannel\Config::get('env.https.hsts_maxage'));
            if(!IS_HTTPS) {
                $this->redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            }
        }

        /*
        if(\Flannel\Input::server('PHP_AUTH_USER') != \Flannel\Config::get('Flannel.api.username')
            || \Flannel\Input::server('PHP_AUTH_PW') != \Flannel\Config::get('Flannel.api.password')) {
            $this->unauthorized();
        }
        */

        parent::__construct();
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
        if(!$this->_payload['success']) {
            header('HTTP/1.1 422 Unprocessable Entity');
        }
        $this->outputJson($this->_payload);
    }

    /**
     * @param mixed $item
     */
    protected function _sendItem($item) {
        $this->_payload['result'] = $this->_translate($item);
        $this->_send();
    }

    /**
     * @param \Flannel\Object[] $collection
     */
    protected function _sendCollection($collection) {
        $data = [];
        foreach($collection as $item) {
            $data[] = $this->_translate($item);
        }

        $this->_payload['result'] = [
            'count' => count($data),
            'items' => $data
        ];
        $this->_send();
    }

    /**
     * @param int $code
     * @param bool $send
     */
    protected function _addError($code, $send=false) {
        $this->_payload['success'] = false;
        $this->_payload['errors'][] = [
            'code' => (int)$code,
            'message' => $this->_errors[$code] ?? 'Unknown error'
        ];
        if($send) {
            $this->_send();
        }
    }

    /**
     * Extended this method to standardize the output.
     * Even if the underlying data model changes, output should remain the same
     *
     * @param \Flannel\Object $item
     * @return mixed[]
     */
    protected function _translate($item) {
        return null;
    }

}
