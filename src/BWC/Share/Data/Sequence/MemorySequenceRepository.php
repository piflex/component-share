<?php

namespace BWC\Share\Data\Sequence;


class MemorySequenceRepository implements SequenceRepositoryInterface
{
    /** @var \BWC\Share\Data\Sequence\SequenceGeneratorInterface */
    private $_generator;

    /** @var array   name => value => true */
    private $_data = array();



    function __construct(SequenceGeneratorInterface $generator) {
        $this->_generator = $generator;
    }


    /**
     * @param string $name
     * @return string
     */
    function generate($name = 'default') {
        $result = null;
        for ($i=0; $i<10; $i++) {
            $value = $this->_generator->generate();
            if (!$this->exists($value, $name)) {
                $result = $value;
                $this->_data[$name][$result] = true;
                break;
            }
        }
        return $result;
    }

    /**
     * @param string $value
     * @param string $name
     * @return bool
     */
    function exists($value, $name = 'default') {
        return @$this->_data[$name][$value];
    }

}