<?php

namespace Flannel\Core;
use Monolog;

// Use \Flannel\Core\Config values db.[link].host, db.[link].dbname, db.[link].user, db.[link].password, db.[link].debug

class Db {

    const TIME_ZONE = 'UTC';
    const CLIENT_CHARSET = 'utf8';

    /**
     * @var mysqli
     */
    protected $_link;

    /**
     * @var string
     */
    protected $_linkName;

    /**
     * @var bool
     */
    protected $_debug;

    /**
     * @var int|null
     */
    protected $_lastInsertId;

    /**
     * @var int|null
     */
    protected $_lastNumRows;

    /**
     * @var int|null
     */
    protected $_lastAffectedRows;

    /**
     * @var int|null
     */
    protected $_lastErrorCode;

    /**
     * @var string|null
     */
    protected $_lastError;

    /**
     * @var \Flannel\Core\Db
     */
    protected static $_links = [];

    /**
     * List of column names
     *
     * @var mixed[]
     */
    protected static $_columnNames = [];

    /**
     * @param string $name
     * @return \Flannel\Core\Db
     */
    public static function getLink($name) {
        if(!isset(static::$_links[$name])) {
            static::$_links[$name] = new static(
                $name,
                \Flannel\Core\Config::get('db.'.$name.'.host'),
                \Flannel\Core\Config::get('db.'.$name.'.user'),
                \Flannel\Core\Config::get('db.'.$name.'.password'),
                \Flannel\Core\Config::get('db.'.$name.'.dbname'),
                \Flannel\Core\Config::get('db.'.$name.'.port') ?? null
            );
        }
        return static::$_links[$name];
    }

    /**
     * Protected - Use Flannel\Core\Db::getLink(...) instead
     * 
     * @param type $linkName
     * @param type $host
     * @param type $username
     * @param type $password
     * @param type $dbname
     * @param type $port
     * @param type $socket
     */
    protected function __construct($linkName, $host, $username, $password, $dbname, $port=null, $socket=null) {
        $this->_linkName = $linkName;
        $this->_link = new \mysqli($host, $username, $password, $dbname, $port, $socket);
        $this->_debug = \Flannel\Core\Config::get('db.'.$this->_linkName.'.debug');

        if($this->_link->connect_error) {
            throw new Exception($this->_link->connect_error);
        }

        $this->_link->set_charset(self::CLIENT_CHARSET);
        $this->query("SET time_zone = '" . $this->_link->real_escape_string(self::TIME_ZONE) . "'");
    }

    /**
     * Close connection
     */
    public function __destruct() {
        try {
            $this->_link->close();
        } catch(Exception $e) {
            //do nothing
        }
    }

    /**
     * Logging
     *
     * @param string $str
     * @param int $level
     */
    public function log($str, $level=\Flannel\Core\Monolog::ERROR) {
        \Flannel\Core\Monolog::get('database')->addRecord($level, $str);
    }

    /**
     * Backwards compatibility from when this class used to extended mysqli
     * 
     * @deprecated
     * 
     * @param string $name
     * @param mixed[] $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        return call_user_func_array(array($this->_link, $name), $arguments);
    }

    /**
     * @return int
     */
    public function getLastInsertId() {
        return $this->_lastInsertId;
    }

    /**
     * @return int
     */
    public function getLastNumRows() {
        return $this->_lastNumRows;
    }

    /**
     * @return int
     */
    public function getLastAffectedRows() {
        return $this->_lastAffectedRows;
    }

    /**
     * @return int
     */
    public function getLastErrorCode() {
        return $this->_lastErrorCode;
    }

    /**
     * @return string
     */
    public function getLastError() {
        return $this->_lastError;
    }

    /**
     * 
     * @param string $sql
     * @param mixed[] $binds
     * @param string $return_type
     * @return mixed
     * @throws Exception
     */
    public function query($sql, $binds=[], $return_type='none') {
        if($this->_debug) {
            $this->log($sql, \Flannel\Core\Monolog::DEBUG);
        }

        $result = $this->_link->query(
            $this->_prepareBinds($sql, $binds)
        );

        $this->_lastInsertId = $this->_link->insert_id ?? null;
        $this->_lastNumRows = $result->num_rows ?? 0;
        $this->_lastAffectedRows = $this->_link->affected_rows ?? 0;
        $this->_lastErrorCode = $this->_link->errno ?? 0;
        $this->_lastError = $this->_link->error ?? '';

        if($this->_lastErrorCode) {
            $this->log(sprintf('[%s] %s -- Query: %s', $this->_lastErrorCode, $this->_lastError, preg_replace('/\s+/', ' ',$sql)));
            throw new Exception(sprintf('[%s] %s', $this->_lastErrorCode, $this->_lastError));
        }

        switch ($return_type) {
            case 'assoc':
            case 'row':
                return $result->fetch_assoc();

            case 'num':
            case 'all':

                //-> @todo: Figure out why this is executing even though I am passing 'none' for $return_type
                if(is_object($result)) {

                    $data = [];
                    while($row = $result->fetch_assoc()) {
                        $data[] = $row;
                    }
                    return $data;
                }
                else {
                    return (bool)$result;
                }

            case 'none':
            default:
                return (bool)$result;
        }
    }

    /**
     *
     * @param string $sql
     * @param mixed[] $binds
     * @return string
     */
    protected function _prepareBinds($sql, $binds) {

        if(!empty($binds)) {
            uksort($binds, 'keyLengthSort');
            
            foreach($binds as $key=>$val) {
                $val = $this->_link->real_escape_string($val);
                /* Commenting this out because of numbers that should be stored as text (i.e. zipcode 02476)
                if(is_numeric($val)) {
                    $sql = str_replace(":$key", $val, $sql);
                } else {
                    $sql = str_replace(":$key", "'$val'", $sql);
                }
                */
                $sql = str_replace(":$key", "'$val'", $sql);
            }
        }

        return $sql;
    }

    /**
     * Safely quote identifiers (eg, column names, table names)
     *
     * @param string $value
     * @return string
     */
    public function quoteIdentifier($value) {
        return '`' . str_replace('`', '``', $value) . '`';
    }

    /**
     * @param string $table
     * @return string[]
     */
    public function getColumnNames($table) {
        if(!isset(static::$_columnNames[$this->_linkName][$table])) {
            if(!isset(static::$_columnNames[$this->_linkName])) {
                static::$_columnNames[$this->_linkName] = [];
            }
            $rows = $this->query('SHOW COLUMNS FROM ' . $this->quoteIdentifier($table), null, 'all');
            static::$_columnNames[$this->_linkName][$table] = array_column($rows, 'Field');
        }
        return static::$_columnNames[$this->_linkName][$table];
    }

    /**
     * Forget column name cache (mainly used after table structure changes)
     */
    public static function clearColumnNames() {
        static::$_columnNames = [];
    }
}
