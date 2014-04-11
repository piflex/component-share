<?php

namespace BWC\Share\Data;

class WhereStringChunk
{

    public $string;

    function __construct($string) {
        $this->string = $string;
    }

    function build() {
        return array($this->string, array());
    }
}
