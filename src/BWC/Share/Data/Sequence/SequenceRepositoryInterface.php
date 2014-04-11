<?php

namespace BWC\Share\Data\Sequence;


interface SequenceRepositoryInterface
{
    /**
     * @param string $name
     * @return string
     */
    function generate($name = 'default');

    /**
     * @param string $value
     * @param string $name
     * @return bool
     */
    function exists($value, $name = 'default');
}