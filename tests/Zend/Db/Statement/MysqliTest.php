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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * @namespace
 */
namespace ZendTest\Db\Statement;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Statement
 */
class MySQLiTest extends AbstractTest
{

    public function setup()
    {
        $this->markTestSkipped('This suite is skipped until Zend\DB can be refactored.');
    }
    
    public function testStatementRowCount()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->prepare("DELETE FROM $products WHERE $product_id = 1");

        $n = $stmt->rowCount();
        $this->assertType('integer', $n);
        $this->assertEquals(-1, $n, 'Expecting row count to be -1 before executing query');

        $stmt->execute();

        $n = $stmt->rowCount();
        $stmt->closeCursor();

        $this->assertType('integer', $n);
        $this->assertEquals(1, $n, 'Expected row count to be one after executing query');
    }

    public function testStatementBindParamByName()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $productIdValue   = 4;
        $productNameValue = 'AmigaOS';

        try {
            $stmt = $this->_db->prepare("INSERT INTO $products ($product_id, $product_name) VALUES (:id, :name)");
            // test with colon prefix
            $this->assertTrue($stmt->bindParam(':id', $productIdValue), 'Expected bindParam(\':id\') to return true');
            // test with no colon prefix
            $this->assertTrue($stmt->bindParam('name', $productNameValue), 'Expected bindParam(\'name\') to return true');
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (\Zend\Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
            $this->assertEquals("Invalid bind-variable name ':id'", $e->getMessage());
        }
    }

    public function testStatementBindValueByName()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $productIdValue   = 4;
        $productNameValue = 'AmigaOS';

        try {
            $stmt = $this->_db->prepare("INSERT INTO $products ($product_id, $product_name) VALUES (:id, :name)");
            // test with colon prefix
            $this->assertTrue($stmt->bindParam(':id', $productIdValue), 'Expected bindParam(\':id\') to return true');
            // test with no colon prefix
            $this->assertTrue($stmt->bindParam('name', $productNameValue), 'Expected bindParam(\'name\') to return true');
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (\Zend\Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
            $this->assertEquals("Invalid bind-variable name ':id'", $e->getMessage());
        }
    }

    public function testStatementGetColumnMeta()
    {
        $this->markTestIncomplete($this->getDriver() . ' has not implemented getColumnMeta() yet [ZF-1424]');
    }

    /**
     * Tests ZF-3216, that the statement object throws exceptions that
     * contain the numerica MySQL SQLSTATE error code
     * @group ZF-3216
     */
    public function testStatementExceptionShouldContainErrorCode()
    {
        $sql = "SELECT * FROM *";
        try {
            $stmt = $this->_db->query($sql);
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (\Zend\Exception $e) {
            $this->assertType('int', $e->getCode());
        }
    }

    /**
     * @group ZF-7706
     */
    public function testStatementCanReturnDriverStatement()
    {
        $statement = parent::testStatementCanReturnDriverStatement();
        $this->assertType('mysqli_stmt', $statement->getDriverStatement());
    }

    /**
     * Tests that the statement returns FALSE when no records are found
     * @group ZF-5675
     */
    public function testStatementReturnsFalseOnEmpty()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $sql = 'SELECT * FROM ' . $products . ' WHERE 1=2';
        $stmt = $this->_db->query($sql);
        $result = $stmt->fetch();
        $this->assertFalse($result);
    }
    
	/**
	 * Test to verify valid report of issue
	 * 
     * @group ZF-8986
     */
    public function testNumberOfBoundParamsDoesNotMatchNumberOfTokens()
    {
    	$this->_util->createTable('zf_objects', array(
            'object_id'		=> 'INTEGER NOT NULL',
    		'object_type'	=> 'INTEGER NOT NULL',
    		'object_status' => 'INTEGER NOT NULL',
    		'object_lati'   => 'REAL',
    		'object_long'   => 'REAL',
        ));
        $tableName = $this->_util->getTableName('zf_objects');
        
        $numRows = $this->_db->insert($tableName, array (
        	'object_id' => 1,
        	'object_type' => 1,
        	'object_status' => 1,
        	'object_lati' => 1.12345,
        	'object_long' => 1.54321,
        ));
        
        $sql = 'SELECT object_id, object_type, object_status,'
             . ' object_lati, object_long FROM ' . $tableName 
             . ' WHERE object_id = ?';
             
        try {
        	$stmt = $this->_db->query($sql, 1);
        } catch (\Exception $e) {
        	$this->fail('Bounding params failed: ' . $e->getMessage());
        }
        $result = $stmt->fetch();
        $this->assertType('array', $result);
        $this->assertEquals(5, count($result));
        $this->assertEquals(1, $result['object_id']);
        $this->assertEquals(1, $result['object_type']);
        $this->assertEquals(1, $result['object_status']);
        $this->assertEquals(1.12345, $result['object_lati']);
        $this->assertEquals(1.54321, $result['object_long']);
    }


    public function getDriver()
    {
        return 'Mysqli';
    }

}
