<?php

class Model_Post extends \Flannel\Core\Db\Row
{

    const STATUS_UPDATED = -1;
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_DISABLED = 2;

    /**
     *
     */
    public function __construct($data = [])
    {
        $this->_initDbTable('app', 'post', 'post_id', [
            'post_id' => [
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
