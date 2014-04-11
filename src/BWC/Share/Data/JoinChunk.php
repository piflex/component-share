<?php

namespace BWC\Share\Data;

class JoinChunk
{
    public $type;
    public $table;
    public $on;
    public $alias;

    function __construct($type, $table, $on, $alias = null) {
        $this->type = $type;
        $this->table = $table;
        $this->on = $on;
        $this->alias = $alias ? $alias : '';
    }

    function build() {
        $result = "{$this->type} JOIN `{$this->table}` `{$this->alias}` ON {$this->on} \n";
        return $result;
    }

}