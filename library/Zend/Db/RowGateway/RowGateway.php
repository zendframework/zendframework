<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\RowGateway;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\Row,
    Zend\Db\ResultSet\RowObjectInterface,
    Zend\Db\Sql\Sql;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage RowGateway
 */
class RowGateway extends AbstractRowGateway
{


    /**
     * Constructor
     * 
     * @param string $tableGateway
     * @param string|Sql\TableIdentifier $table
     * @param Adapter $adapter
     * @param Sql\Sql $sql
     */
    public function __construct($primaryKeyColumn, $table, $adapterOrSql = null)
    {
        $this->primaryKeyColumn = $primaryKeyColumn;
        $this->table = $table;
        if ($adapterOrSql instanceof Sql) {
            $this->sql = $adapterOrSql;
        } elseif ($adapterOrSql instanceof Adapter) {
            $this->sql = new Sql($adapterOrSql, $this->table);
        }
        if ($this->sql == null) {
            throw new Exception\InvalidArgumentException('A valid Sql object was not provided.');
        } elseif ($this->sql->getTable() !== $this->table) {
            throw new Exception\InvalidArgumentException('The Sql object provided does not have a table that matches this row object');
        }

        $this->initialize();
    }

}
