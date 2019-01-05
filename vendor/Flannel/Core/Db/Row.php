<?php

namespace Flannel\Core\Db;

abstract class Row extends \Flannel\Core\BaseObject {

    /**
     * @var \Flannel\Core\Db\Table
     */
    protected $_dbTable;

    /**
     * Add this to all extended classes
     */
    public function __construct($data=[]) {
        //$this->_initDbTable($db, $tableName, $idColumnName, $restrictedColumns);
        return parent::__construct($data);
    }

    /**
     *
     * @param string $dbName
     * @param string $tableName
     * @param string $idColumnName
     * @param mixed[] $restrictedColumns
     * @return self
     */
    protected function _initDbTable($dbName, $tableName, $idColumnName, $restrictedColumns=[]) {
        $this->_idFieldName = $idColumnName;
        $this->_dbTable = new Table($dbName, $tableName, $idColumnName, $restrictedColumns);
        return $this;
    }

    /**
     * @return DatabaseTable
     */
    public function getDbTable() {
        return $this->_dbTable;
    }

    /**
     *
     * @param int $id
     * @param string $columnName
     * @return mixed[]
     */
    public function load($id, $columnName=null) {
        $data = $this->_dbTable->getRow($id, $columnName);
        $this->initData($data ?: []);
        return $this;
    }

    /**
     * @return $this
     */
    public function save() {
        $this->_dbTable->beginTransaction();
        try {
            if(!$this->getId()) {
                $id = $this->_dbTable->insertRow($this->getAllData());
                $this->setId($id);
            } else {
                $this->_dbTable->updateRow($this->getId(), $this->getChangedData());
            }
            $data = $this->_dbTable->getRow($this->getId());
            $this->addData($data);
            $this->initData($this->getAllData());
        } catch(Exception $e) {
            $this->_dbTable->rollBack();
            throw $e;
        }
        $this->_dbTable->commit();
        return $this;
    }

    /**
     * @return $this
     */
    public function delete() {
        if($this->getId()) {
            $this->_dbTable->deleteRow($this->getId());
            $this->initData([]);
        }
        return $this;
    }

    /**
     * @param mixed[] $filters
     * @param mixed[] $orderBy
     * @return static[]
     */
    public static function loadCollection($filters=[], $orderBy=null) {
        $objs = [];

        $rows = (new static())->_dbTable->getRows($filters, $orderBy);
        if(!empty($rows)) {
            foreach($rows as $row) {
                $obj = new static($row);
                $objs[$obj->getId()] = $obj;
            }
        }

        return $objs;
    }

    public static function updateRows($data, $filters=[]) {
        return (new static())->_dbTable->updateRows($data, $filters);
    }
    
}
