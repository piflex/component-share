<?php

namespace BWC\Share\Data;

use Doctrine\DBAL\Connection;

class Select
{

    /** @var string */
    private $_tableName;

    /** @var Connection */
    private $_con;

    /** @var array */
    private $_select;

    /** @var string|null */
    private $_alias;

    /** @var array */
    private $_join = array();

    /** @var array */
    private $_where = array();

    private $_sort = array();

    private $_pageNum = null;
    private $_pageSize = null;

    private $_params = array();


    function __construct($tableName, Connection $connection, $select = null, $alias = null) {
        $this->_tableName = $tableName;
        $this->_con = $connection;
        $this->_select = $select ? (is_array($select) ? $select : array($select)) : array('*');
        $this->_alias = $alias;
    }


    /**
     * @return null|string
     */
    function getAlias() {
        return $this->_alias;
    }


    /**
     * @param array $select
     * @param string $prefix
     * @return Select
     */
    function addSelect(array $select, $prefix = '') {
        foreach ($select as $s) {
            $this->_select[] = "{$prefix}.{$s} as {$prefix}_{$s}";
        }
        return $this;
    }


    function reset() {
        $this->_join = array();
        $this->_where = array();
        $this->_sort = array();
        $this->_pageNum = null;
        $this->_pageSize = null;
        $this->_params = array();
    }

    /**
     * @param $table
     * @param $on
     * @param $alias
     * @return Select
     */
    function join($table, $on, $alias) {
        $this->_join[] = new JoinChunk('', $table, $on, $alias);
        return $this;
    }

    /**
     * @param $table
     * @param $on
     * @param $alias
     * @return Select
     */
    function leftJoin($table, $on, $alias) {
        $this->_join[] = new JoinChunk('LEFT', $table, $on, $alias);
        return $this;
    }

    /**
     * @param array|string $key
     * @param null|mixed $value
     * @param null|string $operator
     * @return Select
     */
    function where($key, $value = null, $operator = null) {
        if (is_array($key)) {
            foreach ($key as $k=>$v) {
                if ($this->_alias && strpos($k, '.')===false) {
                    $k = $this->_alias.'.'.$k;
                }
                $this->_where[] = new WhereChunk($k, $v, $operator);
            }
        } else {
            $this->_where[] = new WhereChunk($key, $value, $operator);
        }
        return $this;
    }


    /**
     * @param $string
     * @return Select
     */
    function whereString($string) {
        $this->_where[] = new WhereStringChunk($string);
        return $this;
    }

    /**
     * @param array|string $column
     * @param null|string $dir
     * @return Select
     */
    function sort($column, $dir = null) {
        if (is_array($column)) {
            foreach ($column as $k=>$v) {
                $this->_sort[$k] = $this->getSortDir($v);
            }
        } else {
            $this->_sort[$column] = '';
        }
        return $this;
    }

    private function getSortDir($dir) {
        if (is_string($dir)) {
            $dir = strtoupper($dir);
            if ($dir != 'ASC' && $dir != 'DESC') {
                $dir = $dir ? 'ASC' : 'DESC';
            }
        } else {
            $dir = $dir ? 'ASC' : 'DESC';
        }
        return $dir;
    }

    /**
     * @param $pageNum int
     * @param $pageSize int
     * @return Select
     */
    function paging($pageNum, $pageSize) {
        $this->_pageNum = intval($pageNum);
        $this->_pageSize = intval($pageSize);
        return $this;
    }


    /**
     * @return string|int
     */
    function getOne() {
        list($sql, $params) = $this->build();
        $result = $this->_con->fetchColumn($sql, $params);
        return $result;
    }


    /**
     * @return array
     */
    function getRow() {
        list($sql, $params) = $this->build();
        $result = $this->_con->fetchAssoc($sql, $params);
        return $result;
    }


    /**
     * @return array
     */
    function getAll() {
        list($sql, $params) = $this->build();
        $result = $this->_con->fetchAll($sql, $params);
        return $result;
    }


    /**
     * @return PagedResult
     */
    function pagedResult() {
        $result = new PagedResult();
        $result->data = $this->getAll();
        $result->totalRows = $this->getFoundRows();
        return $result;
    }


    /**
     * @return int
     */
    function getFoundRows() {
        return $this->_con->fetchColumn('SELECT FOUND_ROWS()');
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return Select
     */
    function setParam($name, $value) {
        $this->_params[$name] = $value;
        return $this;
    }



    private function build() {
        $params = array();
        $sqlSelect = implode(', ', $this->_select);
        $sql = "SELECT SQL_CALC_FOUND_ROWS $sqlSelect \nFROM `{$this->_tableName}` ";
        if (!empty($this->_alias)) {
            $sql .= ' '.$this->_alias;
        }
        if (!empty($this->_join)) {
            $sql .= "\n";
            /** @var $chunk JoinChunk */
            foreach ($this->_join as $chunk) {
                $sql .= $chunk->build();
            }
        }
        if (!empty($this->_where)) {
            $arr = array();
            /** @var $chunk WhereChunk */
            foreach ($this->_where as $chunk) {
                list($wSQL, $wParams) = $chunk->build();
                $arr[] = $wSQL;
                foreach ($wParams as $k=>$v) {
                    $params[$k] = $v;
                }
            }
            $sql .= " WHERE ".implode(' AND ', $arr);
        }
        if (!empty($this->_sort)) {
            $arr = array();
            foreach ($this->_sort as $k=>$v) {
                if ($k) {
                    if ($v) {
                        $arr[] = "{$k} {$v}";
                    } else {
                        $arr[] = "$k";
                    }
                }
            }
            if (!empty($arr)) {
                $sql .= " ORDER BY ";
                $sql .= implode(', ', $arr);
            }
        }
        if ($this->_pageSize) {
            if ($this->_pageNum) {
                $from = ($this->_pageNum - 1) * $this->_pageSize;
                $sql .= " LIMIT {$from}, {$this->_pageSize} ";
            } else {
                $sql .= " LIMIT {$this->_pageSize} ";
            }
        }
        $params = array_merge($params, $this->_params);
        //print $sql;
        return array($sql, $params);
    }
    
}
