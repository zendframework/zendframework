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
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Db_TestUtil_Common
 */
require_once 'Zend/Db/TestUtil/Common.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_TestUtil_Mysqli extends Zend_Db_TestUtil_Common
{

    public function getParams(array $constants = array())
    {
        $constants = array(
            'host'     => 'TESTS_ZEND_DB_ADAPTER_MYSQL_HOSTNAME',
            'username' => 'TESTS_ZEND_DB_ADAPTER_MYSQL_USERNAME',
            'password' => 'TESTS_ZEND_DB_ADAPTER_MYSQL_PASSWORD',
            'dbname'   => 'TESTS_ZEND_DB_ADAPTER_MYSQL_DATABASE',
            'port'     => 'TESTS_ZEND_DB_ADAPTER_MYSQL_PORT'
        );
        return parent::getParams($constants);
    }

    public function getSqlType($type)
    {
        if ($type == 'IDENTITY') {
            return 'INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT';
        }
        if ($type == 'CLOB') {
            return 'TEXT';
        }
        return $type;
    }

    protected function _getSqlCreateTable($tableName)
    {
        return 'CREATE TABLE IF NOT EXISTS ' . $this->_db->quoteIdentifier($tableName);
    }

    protected function _getSqlCreateTableType()
    {
        return ' ENGINE=InnoDB';
    }

    protected function _getSqlDropTable($tableName)
    {
        return 'DROP TABLE IF EXISTS ' . $this->_db->quoteIdentifier($tableName);
    }

    protected function _rawQuery($sql)
    {
        $mysqli = $this->_db->getConnection();
        $retval = $mysqli->query($sql);
        if (!$retval) {
            $e = $mysqli->error;
            require_once 'Zend/Db/Exception.php';
            throw new Zend_Db_Exception("SQL error for \"$sql\": $e");
        }
    }

    /**
     * Test that describeTable() returns correct float type
     * @group ZF-3624
     */
    protected function _getColumnsPrice()
    {
        return array(
            'product_id'    => 'INTEGER NOT NULL',
            'price_name'    => 'VARCHAR(100)',
            'price'         => 'FLOAT(10,8)',
            'price_total'   => 'DECIMAL(10,2) NOT NULL',
            'PRIMARY KEY'   => 'product_id'
            );
    }

    /**
     * Test that describeTable() returns correct float type
     * @group ZF-3624
     */
    protected function _getDataPrice()
    {
        return array(
            array(
                'product_id'   => 1,
                'price_name'   => 'Price 1',
                'price_total'  => 200.45
            )
        );
    }

    public function setUp(Zend_Db_Adapter_Abstract $db)
    {
        parent::setUp($db);

        $this->_createTestProcedure();
    }

    public function tearDown()
    {
        $this->_dropTestProcedure();

        parent::tearDown();
    }

    protected function _createTestProcedure()
    {
        $this->_rawQuery('DROP PROCEDURE IF EXISTS zf_test_procedure');
        $this->_rawQuery('CREATE PROCEDURE zf_test_procedure(IN param1 INTEGER) BEGIN SELECT * FROM zfproducts WHERE product_id = param1; END');
    }

    protected function _dropTestProcedure()
    {
        $this->_rawQuery('DROP PROCEDURE IF EXISTS zf_test_procedure');
    }

    protected function _getSqlCreateView($viewName)
    {
        return 'CREATE OR REPLACE VIEW ' . $this->_db->quoteIdentifier($viewName, true);
    }

    protected function _getSqlDropView($viewName)
    {
        return 'DROP VIEW IF EXISTS ' . $this->_db->quoteIdentifier($viewName, true);
    }
}
