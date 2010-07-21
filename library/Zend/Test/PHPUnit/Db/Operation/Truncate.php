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
namespace Zend\Test\PHPUnit\Db\Operation;

/**
 * Operation for Truncating on setup or teardown of a database tester.
 *
 * @uses       PHPUnit_Extensions_Database_DataSet_IDataSet
 * @uses       PHPUnit_Extensions_Database_DB_IDatabaseConnection
 * @uses       PHPUnit_Extensions_Database_Operation_Exception
 * @uses       PHPUnit_Extensions_Database_Operation_IDatabaseOperation
 * @uses       \Zend\Exception
 * @uses       \Zend\Test\PHPUnit\Db\Connection
 * @uses       \Zend\Test\PHPUnit\Db\Exception
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Truncate implements \PHPUnit_Extensions_Database_Operation_IDatabaseOperation
{
    /**
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet
     * @return void
     */
    public function execute(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, \PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        if(!($connection instanceof \Zend\Test\PHPUnit\Db\Connection)) {
            throw new \Zend\Test\PHPUnit\Db\Exception("Not a valid Zend_Test_PHPUnit_Db_Connection instance, ".get_class($connection)." given!");
        }

        foreach ($dataSet->getReverseIterator() AS $table) {
            try {
                $tableName = $table->getTableMetaData()->getTableName();
                $this->_truncate($connection->getConnection(), $tableName);
            } catch (\Exception $e) {
                throw new \PHPUnit_Extensions_Database_Operation_Exception('TRUNCATE', 'TRUNCATE '.$tableName.'', array(), $table, $e->getMessage());
            }
        }
    }

    /**
     * Truncate a given table.
     *
     * @param \Zend\Db\Adapter\AbstractAdapter $db
     * @param string $tableName
     * @return void
     */
    protected function _truncate(\Zend\Db\Adapter\AbstractAdapter $db, $tableName)
    {
        $tableName = $db->quoteIdentifier($tableName);
        if($db instanceof \Zend\Db\Adapter\Pdo\Sqlite) {
            $db->query('DELETE FROM '.$tableName);
        } else if($db instanceof \Zend\Db\Adapter\Db2) {
            /*if(strstr(PHP_OS, "WIN")) {
                $file = tempnam(sys_get_temp_dir(), "zendtestdbibm_");
                file_put_contents($file, "");
                $db->query('IMPORT FROM '.$file.' OF DEL REPLACE INTO '.$tableName);
                unlink($file);
            } else {
                $db->query('IMPORT FROM /dev/null OF DEL REPLACE INTO '.$tableName);
            }*/
            throw \Zend\Exception("IBM Db2 TRUNCATE not supported.");
        } else if($this->_isMssqlOrOracle($db)) {
            $db->query('TRUNCATE TABLE '.$tableName);
        } else if($db instanceof \Zend\Db\Adapter\Pdo\PgSql) {
            $db->query('TRUNCATE '.$tableName.' CASCADE');
        } else {
            $db->query('TRUNCATE '.$tableName);
        }
    }

    /**
     * Detect if an adapter is for Mssql or Oracle Databases.
     *
     * @param  \Zend\DB\Adapter\AbstractAdapter $db
     * @return bool
     */
    private function _isMssqlOrOracle($db)
    {
        return (
            $db instanceof \Zend\Db\Adapter\Pdo\Mssql ||
            $db instanceof \Zend\Db\Adapter\Sqlsrv ||
            $db instanceof \Zend\Db\Adapter\Pdo\Oci ||
            $db instanceof \Zend\Db\Adapter\Oracle
        );
    }
}
