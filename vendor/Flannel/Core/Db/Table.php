<?php

namespace Flannel\Core\Db;
use Monolog;
use Exception;

class Table {

    /**
     * The database connection instance
     *
     * @var \Flannel\Core\Db
     */
    protected $_db;

    /**
     * @var string
     */
    protected $_tableName;

    /**
     * @var string
     */
    protected $_idColumnName;

    /**
     * Manually enter columns that should not be directly affected
     *
     * $_idColumnName will automatically not allow changes - you do not need to add it here.
     *
     * If you are setting actual values, you need to add quotes yourself.
     * For example: 'insert' => "'FooBar'"
     *
     * Example:
     * $_restrictedColumns = [
     *     'account_id' => [
     *         'update' => false
     *     ],
     *     'created_at' => [
     *         'insert' => 'UTC_TIMESTAMP()',
     *         'update' => false
     *     ],
     *     'updated_at' => [
     *         'insert' => 'UTC_TIMESTAMP()',
     *         'update' => 'UTC_TIMESTAMP()'
     *     ]
     * ]
     *
     * @var mixed[]
     */
    protected $_restrictedColumns = [];

    /**
     * List of column names
     * (lazy-loaded - do not need to manually enter names)
     *
     * @var null|string[]
     */
    protected $_columnNames = null;

    /**
     * @param string $dbName
     * @param string $tableName
     * @param string $idColumnName
     * @param mixed[] $restrictedColumns
     */
    public function __construct($dbName, $tableName, $idColumnName, $restrictedColumns=[]) {
        $this->_db = \Flannel\Core\Db::getLink($dbName);
        $this->_tableName = $tableName;
        $this->_idColumnName = $idColumnName;
        $this->_restrictedColumns = $restrictedColumns;

        if(!$this->_columnNames) {
            $this->_columnNames = $this->_db->getColumnNames($this->_tableName);
        }
    }

    /**
     * Logging
     *
     * @param string $str
     * @param int $level
     */
    public function log($str, $level=\Flannel\Core\Monolog::ERROR) {
        $this->_db->log($str, $level);
    }

    /**
     * Get db connection for manual queries
     * 
     * @return \Flannel\Core\Db
     */
    public function getDb() {
        return $this->_db;
    }

    /**
     *
     */
    public function beginTransaction() {
        $this->_db->autocommit(false);
    }

    /**
     *
     */
    public function commit() {
        $this->_db->commit();
        $this->_db->autocommit(true);
    }

    /**
     *
     */
    public function rollBack() {
        $this->_db->rollback();
        $this->_db->autocommit(true);
    }

    /**
     * Insert a row into table
     *
     * @param mixed[] $data Format: [columnName=>value]
     * @return int
     */
    public function insertRow($data) {
        $this->insertRows([$data]);
        return $this->_db->getLastInsertId();
    }

    /**
     * Insert rows into table
     *
     * @param mixed[] $rows Format: [0 => [columnName=>value]]
     * @param bool $ignoreDups
     * @return int
     */
    public function insertRows($rows, $ignoreDups=false) {
        $firstRow = reset($rows);

        $cols = [];
        foreach(array_keys($firstRow) as $key) {
            if(in_array($key, $this->_columnNames) && $key!==$this->_idColumnName && !isset($this->_restrictedColumns[$key]['insert'])) {
                $cols[] = $key;
            }
        }

        $binds = [];
        $values = [];
        foreach($rows as $row) {
            $bindCount = count($binds);
            $valuesRow = [];
            foreach($cols as $col) {
                $bindKey = $col . '.' . $bindCount;
                $binds[$bindKey] = $row[$col] ?? '';
                $valuesRow[] = ':' . $bindKey;
            }
            $values[] = $valuesRow;
        }

        foreach($this->_restrictedColumns as $columnName=>$actions) {
            if(!empty($actions['insert'])) {
                $cols[] = $columnName;
                foreach($values as $i=>$valuesRow) {
                    $values[$i][] = $actions['insert'];
                }
            }
        }

        foreach($cols as $i=>$col) {
            $cols[$i] = $this->_db->quoteIdentifier($col);
        }

        foreach($values as $i=>$valuesRow) {
            $values[$i] = implode(',', $valuesRow);
        }

        $sql = "INSERT " . ($ignoreDups ? 'IGNORE' : '') . " INTO " . $this->_db->quoteIdentifier($this->_tableName) . " (" . implode(',', $cols) . ")
                     VALUES (" . implode('),(', $values) . ")";

        return $this->_db->query($sql, $binds);
    }

    /**
     * Updates a row in table
     *
     * @param int $id
     * @param mixed[] $data Format: columnName=>value
     * @return bool
     */
    public function updateRow($id, $data) {
        $set = array();
        foreach(array_keys($data) as $key) {
            if(in_array($key, $this->_columnNames) && $key!==$this->_idColumnName && !isset($this->_restrictedColumns[$key]['update'])) {
                $set[] = $this->_db->quoteIdentifier($key) . ' = :' . $key;
            }
        }

        foreach($this->_restrictedColumns as $columnName=>$actions) {
            if(!empty($actions['update'])) {
                $set[] = $this->_db->quoteIdentifier($columnName) . ' = ' . $actions['update'];
            }
        }

        $data[$this->_idColumnName] = (int)$id;

        $sql = "UPDATE " . $this->_db->quoteIdentifier($this->_tableName) . "
                   SET " . implode(', ', $set)  . "
                 WHERE " . $this->_db->quoteIdentifier($this->_idColumnName) . " = :" . $this->_idColumnName . "
                 LIMIT 1";

        return $this->_db->query($sql, $data);
    }

    /**
     * Updates rows in table
     *
     * @todo Write method
     *
     * @param mixed[] $data Format: columnName=>value
     * @param mixed[] $filters (see self::getRows)
     * @return bool
     */
    public function updateRows($data, $filters=[]) {
        $set = array();
        foreach(array_keys($data) as $key) {
            if(in_array($key, $this->_columnNames) && $key!==$this->_idColumnName && !isset($this->_restrictedColumns[$key]['update'])) {
                $set[] = $this->_db->quoteIdentifier($key) . ' = :' . $key;
            }
        }

        foreach($this->_restrictedColumns as $columnName=>$actions) {
            if(!empty($actions['update'])) {
                $set[] = $this->_db->quoteIdentifier($columnName) . ' = ' . $actions['update'];
            }
        }

        $sql = "UPDATE " . $this->_db->quoteIdentifier($this->_tableName) . "
                   SET " . implode(', ', $set)  . "
                 WHERE " . $this->_prepareFilters($filters, $data);;

        return $this->_db->query($sql, $data);

    }

    /**
     * Delete a row from table
     *
     * @param int $id
     * @return bool
     */
    public function deleteRow($id) {
        $data = [$this->_idColumnName => (int)$id];
        $sql = "DELETE
                  FROM " . $this->_db->quoteIdentifier($this->_tableName) . "
                 WHERE " . $this->_db->quoteIdentifier($this->_idColumnName) . " = :" . $this->_idColumnName . "
                 LIMIT 1";

        $this->_db->query($sql, $data);
        return $this->_db->getLastAffectedRows();
    }

    /**
     * Delete row(s) from table
     *
     * @param mixed[] $filters (see self::getRows)
     * @return int
     */
    public function deleteRows($filters=[]) {
        $data = [];

        $sql = "DELETE
                  FROM " . $this->_db->quoteIdentifier($this->_tableName);

        $where = $this->_prepareFilters($filters, $data);
        if($where) {
            $sql .= ' WHERE ' . $where;
        }

        $this->_db->query($sql, $data);
        return $this->_db->getLastAffectedRows();
    }

    /**
     * Get a row from table
     *
     * @param int $id
     * @param string $columnName
     * @return mixed[]
     */
    public function getRow($mixedData, $columnName=null) {
        $binds = [];

        // Support for legacy load and primary key loading
        if (!is_array($mixedData)) {
            if ($columnName === null) {
                $columnName = $this->_idColumnName;
            }
            $binds = [$columnName => $mixedData];            
        } else {
            $binds = $mixedData;
        }

        $where = [];
        foreach ($binds as $columnName => $val) {
            $where[] = $this->_db->quoteIdentifier($columnName) . " = :$columnName";
        }

        $whereClause = '';
        if (!empty($where)) {
            $whereClause = 'WHERE ' . implode(" AND ", $where);
        }

        $sql = "SELECT *
                  FROM " . $this->_db->quoteIdentifier($this->_tableName) . "
                  $whereClause
                 LIMIT 1";
     
        return $this->_db->query($sql, $binds, 'row');
    }

    /**
     * Get row(s) from table
     * 
     * For: `name` = 'Joe Smith'
     * $filters = [
     *     'name' => 'Joe Smith'
     * ]
     *
     * For: `name` LIKE '% Smith'
     * $filters = [
     *     'name' => ['like'=>'Joe Smith']
     * ]
     *
     * For: `name` LIKE '% Smith' AND `is_active` = 1
     * $filters = [
     *     'name' => ['like'=>'Joe Smith'],
     *     'is_active' => 1
     * ]
     *
     * For: `name` LIKE '% Smith' OR `name` LIKE 'Joe %'
     * $filters = [
     *     'name' => [
     *         ['like' => '% Smith'],
     *         ['like' => 'Joe %']
     *     ]
     * ]
     *
     * @param mixed[] $filters
     * @param mixed[] $orderBy Format: columnName=>desc|asc
     * @return mixed[]
     */
    public function getRows($filters=[], $orderBy=null) {
        $data = [];

        $sql = "SELECT *
                  FROM " . $this->_db->quoteIdentifier($this->_tableName);

        $where = $this->_prepareFilters($filters, $data);
        if($where) {
            $sql .= ' WHERE ' . $where;
        }

        if(!empty($orderBy) && is_array($orderBy)) {
            foreach($orderBy as $columnName=>$direction) {
                $orderBy[$columnName] = $this->_db->quoteIdentifier($columnName) . ' ' . ($direction=='ASC' ? 'ASC' : 'DESC');
            }
            $sql .= ' ORDER BY ' . implode(', ', $orderBy);
        }

        return $this->_db->query($sql, $data, 'all');
    }

    /**
     * Pass custom queries to the DB.
     * This is a temporary solution until we can implement joins and limits
     */
    public function query($sql, $binds = array()) {
        return $this->_db->query($sql, $binds, 'all');
    }

    /**
     *
     * @param mixed[] $filters
     * @param array $binds
     * @return string
     * @throws Exception
     */
    protected function _prepareFilters($filters, &$binds) {
        $where = [];
        if(!empty($filters)) {
            foreach($filters as $field=>$conditions) {
                if (in_array($field, $this->_columnNames)) {
                    if(!is_array($conditions)) {
                        $conditions = ['=' => $conditions];
                    }
                    $whereOr = [];
                    $whereAnd = [];
                    foreach($conditions as $cond => $val) {
                        if(is_array($val)) {
                            if ($cond === 'and') {
                                foreach($val as $c => $v) {
                                    $whereAnd[] = $this->_translateConditional($field, $c, $v, $binds);
                                }
                            } elseif ($cond === 'in') {
                                $whereOr[] = $this->_translateConditional($field, $cond, $val, $binds);
                            } else {
                                foreach($val as $c => $v) {
                                    $whereOr[] = $this->_translateConditional($field, $c, $v, $binds);
                                }
                            }
                        } else {
                            $whereOr[] = $this->_translateConditional($field, $cond, $val, $binds);
                        }
                    }

                    if (!empty($whereOr)) {
                        $where[] = '(' . implode(' OR ', $whereOr) . ')';
                    }

                    if (!empty($whereAnd)) {
                        $where[] = '(' . implode(' AND ', $whereAnd) . ')';
                    }
                } else {
                    $msg = sprintf('Column "%s" not found while selecting from "%s"', $field, $this->_tableName);
                    $this->log($msg);
                    throw new Exception($msg);
                }
            }
        }

        return implode(' AND ', $where);
    }

    /**
     *
     * @param string $field
     * @param string $cond
     * @param mixed $val
     * @param array $binds
     * @return string
     */
    protected function _translateConditional($field, $cond, $val, &$binds) {
        $conditionals = [
            '=' => '= VALUE',
            '!=' => '!= VALUE',
            'like' => 'LIKE VALUE',
            'not like' => 'NOT LIKE VALUE',
            'in' => 'IN (VALUE)',
            'not in'   => 'NOT IN (VALUE)',
            'null'  => 'IS NULL',
            'not null'   => 'IS NOT NULL',
            '>'        => '> VALUE',
            '<'        => '< VALUE',
            '>='      => '>= VALUE',
            '<='      => '<= VALUE'
        ];

        switch($cond) {
            case '=':
            case '!=':
            case 'like':
            case 'not like':
            case '>':
            case '<':
            case '>=':
            case '<=':
                $bindKey = $field . '.' . count($binds);
                $binds[$bindKey] = $val;
                return $this->_db->quoteIdentifier($field) . ' ' . str_replace('VALUE', ':'.$bindKey, $conditionals[$cond]);

            case 'in':
            case 'not in':
                $ins = [];
                foreach((array)$val as $v) {
                    $bindKey = $field . '.' . count($binds);
                    $binds[$bindKey] = $v;
                    $ins[] = ':'.$bindKey;
                }
                return $this->_db->quoteIdentifier($field) . ' ' . str_replace('VALUE', implode(',',$ins), $conditionals[$cond]);
            case 'null':
            case 'not null':
                return $this->_db->quoteIdentifier($field) . ' ' . $conditionals[$cond];

            default:
                throw new Exception(sprintf('Condition %s not found', $cond));
        }
    }

}
