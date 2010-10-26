<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Test\PHPUnit\Db\DataSet;
use Zend\Db\Select;

/**
 * Uses several query strings or Zend_Db_Select objects to form a dataset of tables for assertion with other datasets.
 *
 * @uses       PHPUnit_Extensions_Database_DataSet_QueryDataSet
 * @uses       PHPUnit_Extensions_Database_DB_IDatabaseConnection
 * @uses       \Zend\Db\Select
 * @uses       \Zend\Test\PHPUnit\Db\DataSet\QueryTable
 * @uses       \Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class QueryDataSet extends \PHPUnit_Extensions_Database_DataSet_QueryDataSet
{
    /**
     * Creates a new dataset using the given database connection.
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection
     */
    public function __construct(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection)
    {
        if( !($databaseConnection instanceof \Zend\Test\PHPUnit\Db\Connection) ) {
            throw new \Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException(
            	"Zend\Test\PHPUnit\Db\DataSet\QueryDataSet only works with Zend\Test\PHPUnit\Db\Connection connections-"
            );
        }
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * Add a Table dataset representation by specifiying an arbitrary select query.
     *
     * By default a select * will be done on the given tablename.
     *
     * @param string                $tableName
     * @param string|\Zend\Db\Select $query
     */
    public function addTable($tableName, $query = \NULL)
    {
        if ($query === NULL) {
            $query = $this->databaseConnection->getConnection()->select();
            $query->from($tableName, Select::SQL_WILDCARD);
        }

        if($query instanceof Select) {
            $query = $query->__toString();
        }

        $this->tables[$tableName] = new QueryTable($tableName, $query, $this->databaseConnection);
    }
}
