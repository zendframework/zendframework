<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace Zend\Test\PHPUnit\Db\Operation;

/**
 * Delete All Operation that can be executed on set up or tear down of a database tester.
 *
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 */
class DeleteAll implements \PHPUnit_Extensions_Database_Operation_IDatabaseOperation
{
    /**
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet
     */
    public function execute(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, \PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        if(!($connection instanceof \Zend\Test\PHPUnit\Db\Connection)) {
            throw new \Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException(
            	"Not a valid Zend_Test_PHPUnit_Db_Connection instance, ".get_class($connection)." given!"
            );
        }

        foreach ($dataSet as $table) {
            try {
                $tableName = $table->getTableMetaData()->getTableName();
                $connection->getConnection()->delete($tableName);
            } catch (\Exception $e) {
                throw new \PHPUnit_Extensions_Database_Operation_Exception('DELETEALL', 'DELETE FROM '.$tableName.'', array(), $table, $e->getMessage());
            }
        }
    }
}
