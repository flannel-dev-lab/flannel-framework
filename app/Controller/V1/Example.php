<?php

class Controller_V1_Example extends \Flannel\Core\Controller\Api\Standard {

    protected $_isPublic = true;

    /**
     * @var string[]
     */
    protected $_allowedMethods = ['get', 'post'];

    /**
     * @var string[]
     */
    protected $_map = [
        'test_id' => 'id',
        'name'    => 'name',    
    ];

    /**
     * @var string|null
     */
    protected $_resourceName = '';

    /**
     * @var string[]
     */
    protected $_errors = [
        1 => 'All fields must be provided',
    ];


    public function get() {
        $obj = (new \Flannel\Core\BaseObject())->addData([
            'test_id' => 123,
            'name'    => 'Example',
        ]);

        $this->_sendItem($obj);
    }

}
