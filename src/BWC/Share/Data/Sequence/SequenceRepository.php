<?php

namespace BWC\Share\Data\Sequence;

use Doctrine\DBAL\Connection;

abstract class SequenceRepository implements SequenceRepositoryInterface
{
    const MAX_UNIQUE_LOOPS = 20;

    /** @var \Doctrine\DBAL\Connection */
    private $_con;

    /** @var \BWC\Share\Data\Sequence\SequenceGeneratorInterface */
    private $_generator;



    function __construct(Connection $connection, SequenceGeneratorInterface $generator) {
        $this->_con = $connection;
        $this->_generator = $generator;
    }



    abstract function getTableName();


    /**
     * @param string $name
     * @return string
     */
    function generate($name = 'default') {
        $result = null;
        for ($i=0; $i<self::MAX_UNIQUE_LOOPS; $i++) {
            $value = $this->_generator->generate();
            try {
                $this->_con->insert($this->getTableName(), array('name'=>$name, 'value'=>$value));
                $result = $value;
                break;
            } catch (\Exception $ex) { }
        }
        return $result;
    }


    /**
     * @param string $value
     * @param string $name
     * @return bool
     */
    function exists($value, $name = 'default') {
        $tbl = $this->getTableName();
        $count = $this->_con->fetchColumn("SELECT count(*) FROM `$tbl` WHERE name=:name AND value=:value",
            array(':name'=>$name, ':value'=>$value)
        );
        return $count ? true : false;
    }

}