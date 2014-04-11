<?php

namespace BWC\Share\Data;

use BWC\Share\Object\ObjectHelper;
use Doctrine\DBAL\Connection;


abstract class BaseRepository
{
    /** @var \Doctrine\DBAL\Connection */
    protected $_con;

    /** @var int */
    public $totalRows = 0;


    /** @var array */
    private $_classFields = null;



    function __construct(Connection $connection) {
        $this->_con = $connection;
    }


    protected function _getPrimaryKeyColumnName() {
        return 'id';
    }

    protected function _isAutoincrement() {
        return true;
    }

    abstract function _getTableName();


    abstract function _getClassName();



    /**
     * @param object|array|null $row
     * @param string $prefix
     * @return object|null
     */
    function hydrateObject($row, $prefix = '') {
        $result = null;
        if ($row) {
            $class = $this->_getClassName();
            $result = new $class();
            ObjectHelper::copyExistingProperties($row, $result, $prefix);
        }
        return $result;
    }


    protected function _getNonDbFields() {
        return array();
    }


    /**
     * @return array   field=>defaultValue
     */
    protected function getClassFields() {
        if ($this->_classFields === null) {
            $this->_classFields = get_class_vars($this->_getClassName());
            $nonDbFields = $this->_getNonDbFields();
            foreach ($nonDbFields as $f) {
                unset($this->_classFields[$f]);
            }
        }

        return $this->_classFields;
    }


    /**
     * @return string[]
     */
    function getFields() {
        $result = array();
        $arr = $this->getClassFields();
        $arr = array_keys($arr);
        foreach ($arr as $field) {
            $result[] = $field;
        }
        return $result;
    }


    /**
     * @param string $fieldName
     * @return bool
     */
    protected function hasClassField($fieldName) {
        $result = array_key_exists((string)$fieldName, $this->getClassFields());
        return $result;
    }

    /**
     * @param array $array
     * @param string $prefix
     * @return object[]
     */
    function hydrateArray(array $array, $prefix = '') {
        $result = array();
        foreach ($array as $row) {
            $result[] = $this->hydrateObject($row, $prefix);
        }
        return $result;
    }



    /**
     * @return \BWC\Share\Data\Select
     */
    protected function getCommonSelect() {
        return $this->select();
    }


    /**
     * @param int $id
     * @return object|null
     */
    protected function _getByID($id) {
        $select = $this->getCommonSelect();
        $alias = $select->getAlias();
        $name = $alias ? $alias.'.id' : 'id';
        $row = $this->getCommonSelect()->where($name, $id)->getRow();
        return $this->hydrateObject($row, '');
    }


    /**
     * @param array $filter   column=>value
     * @param int $pageNum
     * @param int $pageSize
     * @param array|string|null $orderBy
     * @throws \InvalidArgumentException
     * @return object[]
     */
    protected function _getAllBy(array $filter = null, $pageNum = 0, $pageSize = 0, $orderBy = null) {
        $arrWhere = array();
        $arrKeys = array();
        if ($filter) {
            foreach ($filter as $key=>$value) {
                if (!$this->hasClassField($key)) {
                    throw new \InvalidArgumentException($key);
                }
                $arrWhere[] = "`{$key}`=:{$key}";
                $arrKeys[":{$key}"] = $value;
            }
        }
        $pageSize = intval($pageSize);
        $select = $this->getCommonSelect();
        if ($arrWhere) {
            $select->where($filter);
        }
        if ($orderBy) {
            $select->sort($orderBy);
        }
        if ($pageSize) {
            $select->paging($pageNum, $pageSize);
        }
        $pr = $select->pagedResult();
        $this->totalRows = $pr->totalRows;
        $result = $this->hydrateArray($pr->data);
        return $result;
    }



    /**
     * @param object $object Entity to save
     * @param array|null $unsetColumns
     * @return int Affected rows
     */
    protected function _save($object, array $unsetColumns = null) {
        $data = $this->_getObjectData($object);
        if ($unsetColumns) {
            foreach ($unsetColumns as $col) {
                unset($data[$col]);
            }
        }
        foreach ($this->_getNonDbFields() as $col) {
            unset($data[$col]);
        }
        $pk = $this->_getPrimaryKeyColumnName();
        if (isset($object->$pk) && $object->$pk) {
            $arrID = array($pk=>$object->$pk);
            unset($data[$pk]);
            $affectedRows = $this->_con->update($this->_getTableName(), $data, $arrID);
        } else {
            $affectedRows = $this->_con->insert($this->_getTableName(), $data);
            if ($this->_isAutoincrement()) {
                $object->$pk = $this->_con->lastInsertId();
            }
        }
        return $affectedRows;
    }

    protected function _getObjectData($object)
    {
        return get_object_vars($object);
    }

    /**
     * @param null|array|string $columns
     * @param null|string $alias
     * @return Select
     */
    function select($columns = null, $alias = null) {
        return new Select($this->_getTableName(), $this->_con, $columns, $alias);
    }


    /**
     * @param $id
     * @return int  affected rows
     */
    function deleteByID($id) {
        return $this->_con->delete($this->_getTableName(), array($this->_getPrimaryKeyColumnName() => $id));
    }


    function transactional(\Closure $func) {
        $this->_con->transactional($func);
    }
}