<?php

namespace BWC\Share\Data\Sequence;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

abstract class DoctrineMigration extends AbstractMigration
{

    /**
     * @return string
     */
    abstract function getTableName();


    public function up(Schema $schema)
    {
        $tbl = $this->getTableName();
        /** @var $con Connection */
        $con = $this->connection;
        $con->exec("CREATE TABLE `{$tbl}` (
          `name` varchar(20) NOT NULL,
          `value` char(32) NOT NULL,
          PRIMARY KEY (`name`,`value`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ");

    }

    public function down(Schema $schema)
    {

    }
}
