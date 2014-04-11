<?php

namespace BWC\Share\Data\Sequence;

use Doctrine\DBAL\Connection;

abstract class UUIDSequenceRepository extends SequenceRepository
{
    function __construct(Connection $connection) {
        $generator = new UUIDSequenceGenerator();
        parent::__construct($connection, $generator);
    }
}