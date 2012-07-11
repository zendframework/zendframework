<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace Zend\Test\PHPUnit\Db;

use Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException;

/**
 * Simple Tester for Database Tests when the Abstract Test Case cannot be used.
 *
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 */
class SimpleTester extends \PHPUnit_Extensions_Database_DefaultTester
{
    /**
     * Creates a new default database tester using the given connection.
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     */
    public function __construct(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection)
    {
        if(!($connection instanceof Connection)) {
            throw new InvalidArgumentException("Not a valid Zend_Test_PHPUnit_Db_Connection instance, ".get_class($connection)." given!");
        }

        $this->connection = $connection;
        $this->setUpOperation = new \PHPUnit_Extensions_Database_Operation_Composite(array(
            new Operation\Truncate(),
            new Operation\Insert(),
        ));
        $this->tearDownOperation = \PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }

    /**
     * Set Up the database using the given Dataset and the SetUp strategy "Truncate, then Insert"
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet
     */
    public function setUpDatabase(\PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        $this->setDataSet($dataSet);
        $this->onSetUp();
    }
}
