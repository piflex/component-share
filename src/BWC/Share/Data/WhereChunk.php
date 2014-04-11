<?php

namespace BWC\Share\Data;

class WhereChunk
{

    public $key;
    public $value;
    public $operator;

    function __construct($key, $value, $operator = null) {
        $this->key = $key;
        $this->value = $value;
        $this->operator = $operator;
    }

    function build() {
        $params = array();
        if ($this->value !== null) {
            $op = $this->operator ? $this->operator : '=';
            $paramName = str_replace('.', '_', $this->key);
            $sql = "{$this->key} $op :{$paramName}";
            $params[":{$paramName}"] = $this->value;
        } else {
            $sql = $sql = "{$this->key} IS NULL ";
        }
        return array($sql, $params);
    }
}
