<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\TableGateway;

use Zend\Db\Adapter\Adapter,
    Zend\Db\RowGateway\RowGateway,
    Zend\Db\Sql,
    Zend\Db\ResultSet\ResultSet;

class TableRowGateway extends TableGateway
{
    public function __construct($table, $primaryKey, Adapter $adapter, RowGateway $rowGatewayPrototype = null, Sql\Sql $sql = null)
    {
        if (!(is_string($table) || $table instanceof Sql\TableIdentifier)) {
            throw new Exception\InvalidArgumentException('Table name must be a string or an instance of Zend\Db\Sql\TableIdentifier');
        }

        // set table & adapter
        $this->table = $table;
        $this->adapter = $adapter;

        // create & set the Sql object
        $this->sql = ($sql) ?: new Sql\Sql($this->table);
        if ($this->sql->getTable() != $this->table) {
            throw new Exception\InvalidArgumentException('The table inside the provided Sql object must match the table of this TableGateway');
        }

        // create row gatway
        $rowGatewayPrototype = ($rowGatewayPrototype) ?: new RowGateway($primaryKey, $table, $sql);

        // create select result prototype
        $this->setSelectResultPrototype(new ResultSet($rowGatewayPrototype));
    }

    /**
     * @param Sql\Select $select
     * @return null|ResultSet
     * @throws \RuntimeException
     */
    public function selectWith(Sql\Select $select)
    {
        $selectState = $select->getRawState();
        if ($selectState['table'] != $this->tableName || $selectState['schema'] != $this->schema) {
            throw new Exception\RuntimeException('The table name and schema of the provided select object must match that of the table');
        }
        if ($selectState['join'] != array()) {
            throw new Exception\RuntimeException('The Select object provided to TableRowGateway cannot include join statements.');
        }

        $statement = $this->adapter->createStatement();
        $select->prepareStatement($this->adapter, $statement);

        $result = $statement->execute();
        $resultSet = clone $this->selectResultPrototype;
        $resultSet->setDataSource($result);
        return $resultSet;
    }
}
