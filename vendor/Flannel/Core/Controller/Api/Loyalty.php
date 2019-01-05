<?php

namespace Flannel\Controller\Api;

abstract class Loyalty extends \Flannel\Controller\Api {
   
    protected $_isPublic = false;
 
    /**
     * Required to extend
     *
     * @var string['index', 'get', 'post', 'put', 'delete']
     */
    protected $_allowedMethods = [];

    /**
     * @var mixed[]
     */
    protected $_payload = [
        'success' => true,
        'errors' => [],
        'access_token' => null,
        'public_auth' => null,
        'result' => null
    ];

    /**
     * Required to extend
     *
     * @var string[]
     */
    protected $_map = [];

    /**
     * Required to extend
     *
     * @var string|null
     */
    protected $_resourceName = null;

    /**
     * Optional to extend
     *
     * @var string|null
     */
    protected $_subresourceName = null;

    /**
     * Optional to extend
     *
     * @var string|null
     */
    protected $_subresourceParentFieldName = null;
    
    /**
     * @var Model_Terminal|null
     */
    protected $_customer = null;
    protected $_terminal = null;
    
    /**
     * Optional to extend
     * 
     * @var string[]
     */
    protected $_errors = [
        1 => 'Resource not found',
        2 => 'Subresource not found',
    ];

    /**
     * Optional to extend
     * 
     * @var string[]
     */
    protected $_authErrors = [
        1 => 'Unable to authenicate',
        2 => 'Access token must be provided',
        3 => 'Invalid access token',
        4 => 'This terminal is not in a state to process transactions',
        5 => 'This account has been disabled',
        6 => 'Please reset your password',
        7 => 'Your account has been locked due to too many failures',
        8 => 'Invalid accessing of a customer',
    ];

    /**
     * @var \Flannel\Object|null
     */
    protected $_resource = null;

    /**
     * @var \Flannel\Object|null
     */
    protected $_subresource = null;

    public function __construct() {
        if (!$this->_isPublic) {
            $this->_authenticate();
        }
        parent::__construct();
    }

    protected function _authenticate() {

        if($accessToken = \Flannel\Input::server('HTTP_ACCESS_TOKEN')) {

            try {

                $data = \Flannel\JWT::decode($accessToken);

                if (!isset($data->t)) {
                    $data->t = 'terminal';
                }

                switch($data->t) {

                    case 'terminal':

                        $this->_terminal = (new \Model_Terminal())->load($data->id);

                        // Allow for access tokens to be invalidated
                        if ($this->_terminal->getAccessKey() != $data->ak) {
                            $this->_addAuthError(3);
                        }

                        if ($this->_terminal->getStatus() != \Model_Terminal::STATUS_ENABLED) {
                            $this->_addAuthError(4);
                        }   

                        break;

                    case 'customer':

                        $this->_customer = (new \Model_Customer())->load($data->id);

                        // Allow for access tokens to be invalidated
                        if ($this->_customer->getAccessKey() != $data->ak) {
                            $this->_addAuthError(3);
                        }

                        if ($this->_customer->getCustomerId() != \Flannel\Input::get('resourceId')) {
                            $this->_addAuthError(8);
                        }

                        if ($this->_customer->getStatus() == \Model_Customer::STATUS_DISABLED) {
                            $this->_addAuthError(5);
                        }

                        if ($this->_customer->getStatus() == \Model_Customer::STATUS_PW_RESET) {
                            $this->_addAuthError(6);
                        }

                        if ($this->_customer->getStatus() == \Model_Customer::STATUS_PW_LOCK) {
                            $this->_addAuthError(7);
                        }

                        break;

                    default:
                        $this->_addAuthError(1);
                        break;
                }

                $this->_payload['access_token'] = $accessToken;
            }
            catch(Exception $e) {
                $this->_addAuthError(1);
            }
        }
        else {
            $this->_addAuthError(2);
        }
    }

    /**
     * @param int $code
     * @param bool $send
     */
    protected function _addAuthError($code) {
        $this->_payload['success'] = false;
        $this->_payload['errors'][] = [
            'code' => (int)$code,
            'message' => $this->_authErrors[$code] ?? 'Unknown error'
        ];
        $this->_send();
    }  

    /**
     * Retrieving resources
     */
    public function list() {
        $this->_init(__FUNCTION__);

        if($this->_subresourceName) {
            $collection = $this->_subresourceName::loadCollection([
                $this->_subresourceParentFieldName => $this->_resource->getId()
            ]);
        } else {
            $collection = $this->_resourceName::loadCollection([]);
        }
        
        $this->_sendCollection($collection);
    }

    /**
     * Retrieving a single resources
     */
    public function get() {
        $this->_init(__FUNCTION__);
        $this->_sendItem($this->_subresource ?: $this->_resource);
    }

    /**
     * Creating resources
     *
     * @todo Write method
     */
    public function post() {
        $this->_init(__FUNCTION__);
        throw new Exception('Not yet implemented');
    }

    /**
     * Updating resources
     *
     * @todo Write method
     */
    public function put() {
        $this->_init(__FUNCTION__);
        throw new Exception('Not yet implemented');
    }

    /**
     * Deleting resources
     *
     * @todo Write method
     */
    public function delete() {
        $this->_init(__FUNCTION__);
        throw new Exception('Not yet implemented');
    }

    /**
     * @param string $method
     */
    public function _init($method) {
        if(!in_array($method, $this->_allowedMethods)) {
            $this->methodNotAllowed();
        }
        $this->_initResource($method);
        $this->_initSubresource($method);
        return $this;
    }

    /**
     * @param string $method
     */
    protected function _initResource($method) {
        if($this->_subresourceName || ($this->_resourceName && in_array($method, ['get','put','delete']))) {
            $this->_resource = (new $this->_resourceName())->load(\Flannel\Input::get('resourceId'));
            if(!$this->_resource->getId()) {
                $this->_addError(1, true);
            }
        }
        return $this;
    }

    /**
     * @param string $method
     */
    protected function _initSubresource($method) {
        if($this->_subresourceName && in_array($method, ['get','put','delete'])) {
            $this->_subresource = (new $this->_subresourceName())->load(\Flannel\Input::get('subresourceId'));
            if(!$this->_subresource->getId() || $this->_subresource->getData($this->_subresourceParentFieldName) != $this->_resource->getId()) {
                $this->_addError(2, true);
            }
        }
        return $this;
    }

    /**
     * @param \Flannel\Object $item
     * @return mixed[]
     */
    protected function _translate($item) {
        $data = [];
        foreach($this->_map as $objKey=>$apiKey) {
            $data[$apiKey] = $item->getData($objKey);
        }
        return $data;
    }

    protected function _setPublicAuth($publicKey, $message) {
        $this->_payload['public_auth'] = $publicKey . '|' . $message;
    }
}
