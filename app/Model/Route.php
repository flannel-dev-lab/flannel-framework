<?php

class Model_Route extends \Flannel\Core\Db\Row
{

    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    /**
     *
     */
    public function __construct($data = [])
    {
        $this->_initDbTable('app', 'route', 'route_id', [
            'route_id' => [
                'update' => false
            ],
            'created_at' => [
                'insert' => 'UTC_TIMESTAMP()',
                'update' => false,
            ],
        ]);

        return parent::__construct($data);
    }

}
