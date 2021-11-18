<?php

class Model_Template extends \Flannel\Core\Db\Row
{

    /**
     *
     */
    public function __construct($data = [])
    {
        $this->_initDbTable('app', 'template', 'template_id', [
            'template_id' => [
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
