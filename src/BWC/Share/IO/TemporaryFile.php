<?php

namespace BWC\Share\IO;


class TemporaryFile
{
    /** @var string */
    private $_filename;


    /**
     * @param string|null $prefix
     * @return TemporaryFile
     */
    static function create($prefix = null) {
        $fn = tempnam(sys_get_temp_dir(), $prefix);
        $result = new TemporaryFile($fn);
        return $result;
    }


    /**
     * @param string $filename
     * @throws \InvalidArgumentException
     */
    function __construct($filename) {
        if (!is_string($filename)) throw new \InvalidArgumentException('Filename must be a string');
        $this->_filename = $filename;
    }



    function __destruct() {
        if (is_file($this->_filename)) {
            unlink($this->_filename);
        }
    }


    /**
     * @return string
     */
    function getFilename() {
        return $this->_filename;
    }

}