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
 * @version    $Id$
 */

/**
 * @see Zend_Db_TestSetup
 */
require_once 'Zend/Db/TestSetup.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Adapter_TestCommon extends Zend_Db_TestSetup
{
    /**
     * Test AUTO_QUOTE_IDENTIFIERS option
     * Case: Zend_Db::AUTO_QUOTE_IDENTIFIERS = true
     */
    public function testAdapterZendConfig()
    {
        Zend_Loader::loadClass('Zend_Config');
        $params = new Zend_Config($this->_util->getParams());

        $db = Zend_Db::factory($this->getDriver(), $params);
        $db->getConnection();
    }

    /**
     * Test empty adapterNamespace issue
     * Case: Zend_Db::AUTO_QUOTE_IDENTIFIERS = true
     */
    public function testAdapterZendConfigEmptyNamespace()
    {
        Zend_Loader::loadClass('Zend_Config');
        $params = $this->_util->getParams();
        $params['adapterNamespace'] = '';
        $params = new Zend_Config($params);

        $db = Zend_Db::factory($this->getDriver(), $params);
        $db->getConnection();
    }

    /**
     * Test empty driver_options issue
     * Case: $config['driver_options'] == ''
     */
    public function testAdapterZendConfigEmptyDriverOptions()
    {
        Zend_Loader::loadClass('Zend_Config');
        $params = $this->_util->getParams();
        $params['driver_options'] = '';
        $params = new Zend_Config($params);

        $db = Zend_Db::factory($this->getDriver(), $params);
        $db->getConnection();

        $config = $db->getConfig();
        $this->assertEquals(array(), $config['driver_options']);
    }

    /**
     * Test AUTO_QUOTE_IDENTIFIERS option
     * Case: Zend_Db::AUTO_QUOTE_IDENTIFIERS = true
     */
    public function testAdapterAutoQuoteIdentifiersTrue()
    {
        $params = $this->_util->getParams();

        $params['options'] = array(
            Zend_Db::AUTO_QUOTE_IDENTIFIERS => true
        );
        $db = Zend_Db::factory($this->getDriver(), $params);
        $db->getConnection();

        $select = $this->_db->select();
        $select->from('zfproducts');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result), 'Expected 3 rows in first query result');

        $this->assertEquals(1, $result[0]['product_id']);

        $select = $this->_db->select();
        $select->from('ZFPRODUCTS');
        try {
            $stmt = $this->_db->query($select);
            $result = $stmt->fetchAll();
            $this->fail('Expected exception not thrown');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
        }
    }

    /**
     * Test AUTO_QUOTE_IDENTIFIERS option
     * Case: Zend_Db::AUTO_QUOTE_IDENTIFIERS = false
     */
    public function testAdapterAutoQuoteIdentifiersFalse()
    {
        $params = $this->_util->getParams();

        $params['options'] = array(
            Zend_Db::AUTO_QUOTE_IDENTIFIERS => false
        );
        $db = Zend_Db::factory($this->getDriver(), $params);
        $db->getConnection();

        // create a new util object, with the new db adapter
        $driver = $this->getDriver();
        $utilClass = "Zend_Db_TestUtil_{$driver}";
        $util = new $utilClass();
        $util->setAdapter($db);

        // create test table using no identifier quoting
        $util->createTable('noquote', array(
            'id'    => 'INT NOT NULL PRIMARY KEY',
            'stuff' => 'CHAR(10)'
        ));
        $tableName = $this->_util->getTableName('noquote');

        // insert into the table
        $numRows = $db->insert($tableName, array(
            'id'    => 1,
            'stuff' => 'no quote 1'
        ));
        $this->assertEquals(1, $numRows,
            'number of rows in first insert not as expected');

        // check if the row was inserted as expected
        $sql = "SELECT id, stuff FROM $tableName ORDER BY id";
        $stmt = $db->query($sql);
        $fetched = $stmt->fetchAll(Zend_Db::FETCH_NUM);
        $a = array(
            0 => array(0 => 1, 1 => 'no quote 1')
        );
        $this->assertEquals($a, $fetched,
            'result of first query not as expected');

        // insert into the table using other case
        $numRows = $db->insert($tableName, array(
            'ID'    => 2,
            'STUFF' => 'no quote 2'
        ));
        $this->assertEquals(1, $numRows,
            'number of rows in second insert not as expected');

        // check if the row was inserted as expected
        $sql = 'SELECT ID, STUFF FROM ' . $tableName . ' ORDER BY ID';
        $stmt = $db->query($sql);
        $fetched = $stmt->fetchAll(Zend_Db::FETCH_NUM);

        $a = array(
            0 => array(0 => 1, 1 => 'no quote 1'),
            1 => array(0 => 2, 1 => 'no quote 2'),
        );
        $this->assertEquals($a, $fetched,
            'result of second query not as expected');

        // clean up
        unset($stmt);
        $util->dropTable($tableName);
    }


    protected function _testAdapterConstructInvalidParam($param, $adapterClass = null)
    {
        $exceptionClass = 'Zend_Db_Adapter_Exception';
        if ($adapterClass === null) {
            $adapterClass = 'Zend_Db_Adapter_' . $this->getDriver();
        }

        $params = $this->_util->getParams();
        unset($params[$param]);

        Zend_Loader::loadClass($adapterClass);
        Zend_Loader::loadClass($exceptionClass);

        try {
            $db = new $adapterClass($params);
            $db->getConnection(); // force a connection
            $this->fail("Expected to catch $exceptionClass");
        } catch (Zend_Exception $e) {
            $this->assertType($exceptionClass, $e, "Expected to catch $exceptionClass, got ".get_class($e));
            $this->assertContains("Configuration array must have a key for '$param'", $e->getMessage());
        }
    }

    public function testAdapterConstructInvalidParamDbnameException()
    {
        $this->_testAdapterConstructInvalidParam('dbname');
    }

    public function testAdapterConstructInvalidParamUsernameException()
    {
        $this->_testAdapterConstructInvalidParam('username');
    }

    public function testAdapterConstructInvalidParamPasswordException()
    {
        $this->_testAdapterConstructInvalidParam('password');
    }

    /**
     * Test Adapter's delete() method.
     * Delete one row from test table, and verify it was deleted.
     * Then try to delete a row that doesn't exist, and verify it had no effect.
     */
    public function testAdapterDelete()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()->from('zfproducts')->order('product_id ASC');
        $result = $this->_db->fetchAll($select);

        $this->assertEquals(3, count($result), 'Expected count of result to be 2');
        $this->assertEquals(1, $result[0]['product_id'], 'Expecting product_id of 0th row to be 1');

        $rowsAffected = $this->_db->delete('zfproducts', "$product_id = 2");
        $this->assertEquals(1, $rowsAffected, 'Expecting rows affected to be 1');

        $select = $this->_db->select()->from('zfproducts')->order('product_id ASC');
        $result = $this->_db->fetchAll($select);

        $this->assertEquals(2, count($result), 'Expected count of result to be 2');
        $this->assertEquals(1, $result[0]['product_id'], 'Expecting product_id of 0th row to be 1');

        $rowsAffected = $this->_db->delete('zfproducts', "$product_id = 327");
        $this->assertEquals(0, $rowsAffected, 'Expected rows affected to return 0');
    }

    public function testAdapterDeleteWhereArray()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $ids = $this->_db->fetchCol("SELECT $product_id FROM $products ORDER BY $product_id");
        $this->assertEquals(array(1, 2, 3), $ids);

        $rowsAffected = $this->_db->delete(
            'zfproducts',
            array("$product_id = 1", "$product_name = 'Windows'")
        );
        $this->assertEquals(1, $rowsAffected);

        $ids = $this->_db->fetchCol("SELECT $product_id FROM $products ORDER BY $product_id");
        $this->assertEquals(array(2, 3), $ids);
    }

    /**
     * @group ZF-1726
     */
    public function testAdapterDeleteWhereArrayWithVariable()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $ids = $this->_db->fetchCol("SELECT $product_id FROM $products ORDER BY $product_id");
        $this->assertEquals(array(1, 2, 3), $ids);

        $rowsAffected = $this->_db->delete(
            'zfproducts',
            array("$product_id = ?" => 1, "$product_name = ?" => 'Windows')
        );
        $this->assertEquals(1, $rowsAffected);

        $ids = $this->_db->fetchCol("SELECT $product_id FROM $products ORDER BY $product_id");
        $this->assertEquals(array(2, 3), $ids);
    }

    public function testAdapterDeleteWhereDbExpr()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $expr = new Zend_Db_Expr("$product_id = 1");

        $ids = $this->_db->fetchCol("SELECT $product_id FROM $products ORDER BY $product_id");
        $this->assertEquals(array(1, 2, 3), $ids);

        $rowsAffected = $this->_db->delete(
            'zfproducts',
            $expr
        );
        $this->assertEquals(1, $rowsAffected);

        $ids = $this->_db->fetchCol("SELECT $product_id FROM $products ORDER BY $product_id");
        $this->assertEquals(array(2, 3), $ids);
    }

    public function testAdapterDeleteEmptyWhere()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');

        $count = $this->_db->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(4, $count);

        $rowsAffected = $this->_db->delete(
            'zfbugs'
            // intentionally no where clause
        );

        $count = $this->_db->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(0, $count);
    }

    public function testAdapterDescribeTableAttributeColumn()
    {
        $desc = $this->_db->describeTable('zfproducts');

        $this->assertEquals('zfproducts',        $desc['product_name']['TABLE_NAME'], 'Expected table name to be zfproducts');
        $this->assertEquals('product_name',      $desc['product_name']['COLUMN_NAME'], 'Expected column name to be product_name');
        $this->assertEquals(2,                   $desc['product_name']['COLUMN_POSITION'], 'Expected column position to be 2');
        $this->assertRegExp('/varchar/i',        $desc['product_name']['DATA_TYPE'], 'Expected data type to be VARCHAR');
        $this->assertEquals('',                  $desc['product_name']['DEFAULT'], 'Expected default to be empty string');
        $this->assertTrue(                       $desc['product_name']['NULLABLE'], 'Expected product_name to be nullable');
        $this->assertNull(                       $desc['product_name']['SCALE'], 'Expected scale to be null');
        $this->assertNull(                       $desc['product_name']['PRECISION'], 'Expected precision to be null');
        $this->assertFalse(                      $desc['product_name']['PRIMARY'], 'Expected product_name not to be a primary key');
        $this->assertNull(                       $desc['product_name']['PRIMARY_POSITION'], 'Expected product_name to return null for PRIMARY_POSITION');
        $this->assertFalse(                      $desc['product_name']['IDENTITY'], 'Expected product_name to return false for IDENTITY');
    }

    /**
     * Test Adapter's describeTable() method.
     * Retrieve the adapter's description of the test table and examine it.
     */
    public function testAdapterDescribeTableHasColumns()
    {
        $desc = $this->_db->describeTable('zfproducts');

        $cols = array(
            'product_id',
            'product_name'
        );
        $this->assertEquals($cols, array_keys($desc));
    }

    public function testAdapterDescribeTableMetadataFields()
    {
        $desc = $this->_db->describeTable('zfproducts');

        $keys = array(
            'SCHEMA_NAME',
            'TABLE_NAME',
            'COLUMN_NAME',
            'COLUMN_POSITION',
            'DATA_TYPE',
            'DEFAULT',
            'NULLABLE',
            'LENGTH',
            'SCALE',
            'PRECISION',
            'UNSIGNED',
            'PRIMARY',
            'PRIMARY_POSITION',
            'IDENTITY'
        );
        $this->assertEquals($keys, array_keys($desc['product_name']));
    }

    /**
     * Test that an auto-increment key reports itself.
     * This is not supported in all RDBMS brands, so we test
     * it separately from the full describe tests above.
     */
    public function testAdapterDescribeTablePrimaryAuto()
    {
        $desc = $this->_db->describeTable('zfproducts');

        $auto = $desc['product_id']['IDENTITY'];
        if ($auto === null) {
            $this->markTestIncomplete($this->getDriver() . ' needs to learn how to discover auto-increment keys');
        }
        $this->assertTrue($desc['product_id']['IDENTITY']);
    }

    public function testAdapterDescribeTablePrimaryKeyColumn()
    {
        $desc = $this->_db->describeTable('zfproducts');

        $this->assertEquals('zfproducts',        $desc['product_id']['TABLE_NAME'], 'Expected table name to be zfproducts');
        $this->assertEquals('product_id',        $desc['product_id']['COLUMN_NAME'], 'Expected column name to be product_id');
        $this->assertEquals(1,                   $desc['product_id']['COLUMN_POSITION'], 'Expected column position to be 1');
        $this->assertEquals('',                  $desc['product_id']['DEFAULT'], 'Expected default to be empty string');
        $this->assertFalse(                      $desc['product_id']['NULLABLE'], 'Expected product_id not to be nullable');
        $this->assertNull(                       $desc['product_id']['SCALE'], 'Expected scale to be null');
        $this->assertNull(                       $desc['product_id']['PRECISION'], 'Expected precision to be null');
        $this->assertTrue(                       $desc['product_id']['PRIMARY'], 'Expected product_id to be a primary key');
        $this->assertEquals(1,                   $desc['product_id']['PRIMARY_POSITION']);
    }

    /**
     * @group ZF-2927
     */
    public function testAdapterDescribeView()
    {
        $describe = $this->_db->describeTable('temp_view');
        $this->assertEquals(8, count($describe));
    }

    /**
     * Test the Adapter's fetchAll() method.
     */
    public function testAdapterFetchAll()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $result = $this->_db->fetchAll("SELECT * FROM $products WHERE $product_id > ? ORDER BY $product_id ASC", 1);
        $this->assertEquals(2, count($result));
        $this->assertEquals('2', $result[0]['product_id']);
    }

    /**
     * Test that fetchAssoc() still fetched an associative array
     * after the adapter's default fetch mode is set to something else.
     */
    public function testAdapterFetchAllOverrideFetchMode()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $col_name = $this->_db->foldCase('product_id');

        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);

        // Test associative array
        $result = $this->_db->fetchAll("SELECT * FROM $products WHERE $product_id > ? ORDER BY $product_id ASC", 1, Zend_Db::FETCH_ASSOC);
        $this->assertEquals(2, count($result));
        $this->assertType('array', $result[0]);
        $this->assertEquals(2, count($result[0])); // count columns
        $this->assertEquals(2, $result[0][$col_name]);

        // Test numeric and associative array
        $result = $this->_db->fetchAll("SELECT * FROM $products WHERE $product_id > ? ORDER BY $product_id ASC", 1, Zend_Db::FETCH_BOTH);
        $this->assertEquals(2, count($result));
        $this->assertType('array', $result[0]);
        $this->assertEquals(4, count($result[0])); // count columns
        $this->assertEquals(2, $result[0][$col_name]);
        $this->assertEquals(2, $result[0][0]);

        // Ensure original fetch mode has been retained
        $result = $this->_db->fetchAll("SELECT * FROM $products WHERE $product_id > ? ORDER BY $product_id", 1);
        $this->assertEquals(2, count($result));
        $this->assertType('object', $result[0]);
        $this->assertEquals(2, $result[0]->$col_name);
    }

    /**
     * Test the Adapter's fetchAssoc() method.
     */
    public function testAdapterFetchAssoc()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $result = $this->_db->fetchAssoc("SELECT * FROM $products WHERE $product_id > ? ORDER BY $product_id DESC", 1);
        foreach ($result as $idKey => $row) {
            $this->assertEquals($idKey, $row['product_id']);
        }
    }

    /**
     * Test that fetchAssoc() still fetched an associative array
     * after the adapter's default fetch mode is set to something else.
     */
    public function testAdapterFetchAssocAfterSetFetchMode()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $this->_db->fetchAssoc("SELECT * FROM $products WHERE $product_id > ? ORDER BY $product_id DESC", 1);
        $this->assertType('array', $result);
        $this->assertEquals(array('product_id', 'product_name'), array_keys(current($result)));
    }

    /**
     * Test the Adapter's fetchCol() method.
     */
    public function testAdapterFetchCol()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $result = $this->_db->fetchCol("SELECT * FROM $products WHERE $product_id > ? ORDER BY $product_id ASC", 1);
        $this->assertType('array', $result);
        $this->assertEquals(2, count($result)); // count rows
        $this->assertEquals(2, $result[0]);
        $this->assertEquals(3, $result[1]);
    }

    /**
     * Test that fetchCol() still fetched an associative array
     * after the adapter's default fetch mode is set to something else.
     */
    public function testAdapterFetchColAfterSetFetchMode()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $this->_db->fetchCol("SELECT * FROM $products WHERE $product_id > ? ORDER BY $product_id ASC", 1);
        $this->assertType('array', $result);
        $this->assertEquals(2, count($result)); // count rows
        $this->assertEquals(2, $result[0]);
        $this->assertEquals(3, $result[1]);
    }

    /**
     * Test the Adapter's fetchOne() method.
     */
    public function testAdapterFetchOne()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $prod = 'Linux';
        $result = $this->_db->fetchOne("SELECT $product_name FROM $products WHERE $product_id > ? ORDER BY $product_id", 1);
        $this->assertEquals($prod, $result);
    }

    /**
     * Test that fetchCol() still fetched an associative array
     * after the adapter's default fetch mode is set to something else.
     */
    public function testAdapterFetchOneAfterSetFetchMode()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
        $prod = 'Linux';
        $result = $this->_db->fetchOne("SELECT $product_name FROM $products WHERE $product_id > ? ORDER BY $product_id", 1);
        $this->assertType('string', $result);
        $this->assertEquals($prod, $result);
    }

    /**
     * Test the Adapter's fetchPairs() method.
     */
    public function testAdapterFetchPairs()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $prod = 'Linux';
        $result = $this->_db->fetchPairs("SELECT $product_id, $product_name FROM $products WHERE $product_id > ? ORDER BY $product_id ASC", 1);
        $this->assertEquals(2, count($result)); // count rows
        $this->assertEquals($prod, $result[2]);
    }

    /**
     * Test the Adapter's fetchPairs() method.
     */
    public function testAdapterFetchPairsAfterSetFetchMode()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
        $prod = 'Linux';
        $result = $this->_db->fetchPairs("SELECT $product_id, $product_name FROM $products WHERE $product_id > ? ORDER BY $product_id ASC", 1);
        $this->assertType('array', $result);
        $this->assertEquals(2, count($result)); // count rows
        $this->assertEquals($prod, $result[2]);
    }

    /**
     * Test the Adapter's fetchRow() method.
     */
    public function testAdapterFetchRow()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $result = $this->_db->fetchRow("SELECT * FROM $products WHERE $product_id > ? ORDER BY $product_id", 1);
        $this->assertEquals(2, count($result)); // count columns
        $this->assertEquals(2, $result['product_id']);
    }

    /**
     * Test that fetchAssoc() still fetched an associative array
     * after the adapter's default fetch mode is set to something else.
     */
    public function testAdapterFetchRowOverrideFetchMode()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $col_name = $this->_db->foldCase('product_id');

        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);

        // Test associative array
        $result = $this->_db->fetchRow("SELECT * FROM $products WHERE $product_id > ? ORDER BY $product_id", 1, Zend_Db::FETCH_ASSOC);
        $this->assertType('array', $result);
        $this->assertEquals(2, count($result)); // count columns
        $this->assertEquals(2, $result['product_id']);

        // Test numeric and associative array
        $result = $this->_db->fetchRow("SELECT * FROM $products WHERE $product_id > ? ORDER BY $product_id", 1, Zend_Db::FETCH_BOTH);
        $this->assertType('array', $result);
        $this->assertEquals(4, count($result)); // count columns
        $this->assertEquals(2, $result['product_id']);
        $this->assertEquals(2, $result[0]);

        // Ensure original fetch mode has been retained
        $result = $this->_db->fetchRow("SELECT * FROM $products WHERE $product_id > ? ORDER BY $product_id", 1);
        $this->assertType('object', $result);
        $this->assertEquals(2, $result->$col_name);
    }

    /**
     * Test the Adapter's insert() method.
     * This requires providing an associative array of column=>value pairs.
     */
    public function testAdapterInsert()
    {
        $row = array (
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => '2007-04-02',
            'updated_on'      => '2007-04-02',
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy',
            'verified_by'     => 'dduck'
        );
        $rowsAffected = $this->_db->insert('zfbugs', $row);
        $this->assertEquals(1, $rowsAffected);
        $lastInsertId = $this->_db->lastInsertId();
        $this->assertType('string', $lastInsertId);
        $this->assertEquals('5', (string) $lastInsertId,
            'Expected new id to be 5');
    }

    public function testAdapterInsertSequence()
    {
        $row = array (
            'product_id' => $this->_db->nextSequenceId('zfproducts_seq'),
            'product_name' => 'Solaris',
        );
        $rowsAffected = $this->_db->insert('zfproducts', $row);
        $this->assertEquals(1, $rowsAffected);
        $lastInsertId = $this->_db->lastInsertId('zfproducts');
        $lastSequenceId = $this->_db->lastSequenceId('zfproducts_seq');
        $this->assertType('string', $lastInsertId);
        $this->assertType('string', $lastSequenceId);
        $this->assertEquals('4', (string) $lastInsertId, 'Expected new id to be 4');
    }

    public function testAdapterInsertDbExpr()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $expr = new Zend_Db_Expr('2+3');

        $row = array (
            'bug_id'          => $expr,
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => '2007-04-02',
            'updated_on'      => '2007-04-02',
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy',
            'verified_by'     => 'dduck'
        );
        $rowsAffected = $this->_db->insert('zfbugs', $row);
        $this->assertEquals(1, $rowsAffected);

        $value = $this->_db->fetchOne("SELECT $bug_id FROM $bugs WHERE $bug_id = 5");
        $this->assertEquals(5, $value);
    }

    /**
     * Test the Adapter's limit() method.
     * Fetch 1 row.  Then fetch 1 row offset by 1 row.
     * @group ZF-4246
     */
    public function testAdapterLimit()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $sql = $this->_db->limit("SELECT * FROM $products ORDER BY $product_id", 1);

        $stmt = $this->_db->query($sql);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result),
            'Expecting row count to be 1');
        $this->assertEquals(2, count($result[0]),
            'Expecting column count to be 2');
        $this->assertEquals(1, $result[0]['product_id'],
            'Expecting to get product_id 1');

        // Check that extra field ZEND_DB_ROWNUM isn't present
        // (particulary with Db2 & Oracle)
        $this->assertArrayNotHasKey('zend_db_rownum', $result[0]);
        $this->assertArrayNotHasKey('ZEND_DB_ROWNUM', $result[0]);
    }

    /**
     * @group ZF-4246
     */
    public function testAdapterLimitOffset()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $sql = $this->_db->limit("SELECT * FROM $products ORDER BY $product_id", 1, 1);

        $stmt = $this->_db->query($sql);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result),
            'Expecting row count to be 1');
        $this->assertEquals(2, count($result[0]),
            'Expecting column count to be 2');
        $this->assertEquals(2, $result[0]['product_id'],
            'Expecting to get product_id 2');

        // Check that extra field ZEND_DB_ROWNUM isn't present
        // (particulary with Db2 & Oracle)
        $this->assertArrayNotHasKey('zend_db_rownum', $result[0]);
        $this->assertArrayNotHasKey('ZEND_DB_ROWNUM', $result[0]);
    }

    public function testAdapterLimitInvalidArgumentException()
    {
        try {
            $sql = $this->_db->limit('SELECT * FROM zfproducts', 0);
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Adapter_Exception', $e,
                'Expecting object of type Zend_Db_Adapter_Exception, got '.get_class($e));
        }

        try {
            $sql = $this->_db->limit('SELECT * FROM zfproducts', 1, -1);
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Adapter_Exception', $e,
                'Expecting object of type Zend_Db_Adapter_Exception, got '.get_class($e));
        }
    }


    /**
     * Test the Adapter's listTables() method.
     * Fetch the list of tables and verify that the test table exists in
     * the list.
     */
    public function testAdapterListTables()
    {
        $tables = $this->_db->listTables();
        $this->assertContains('zfproducts', $tables);
    }

    /**
     * Used by _testAdapterOptionCaseFoldingNatural()
     * DB2 and Oracle return identifiers in uppercase naturally,
     * so those test suites will override this method.
     */
    protected function _testAdapterOptionCaseFoldingNaturalIdentifier()
    {
        return 'case_folded_identifier';
    }

    /**
     * Used by _testAdapterOptionCaseFoldingNatural()
     * SQLite needs to do metadata setup,
     * because it uses the in-memory database,
     * so that test suite will override this method.
     */
    protected function _testAdapterOptionCaseFoldingSetup(Zend_Db_Adapter_Abstract $db)
    {
        $db->getConnection();
    }

    /**
     * Used by:
     * - testAdapterOptionCaseFoldingNatural()
     * - testAdapterOptionCaseFoldingUpper()
     * - testAdapterOptionCaseFoldingLower()
     *
     * @param int $case
     * @return string
     */
    public function _testAdapterOptionCaseFoldingCommon($case)
    {
        $params = $this->_util->getParams();

        $params['options'] = array(
            Zend_Db::CASE_FOLDING => $case
        );
        $db = Zend_Db::factory($this->getDriver(), $params);
        $this->_testAdapterOptionCaseFoldingSetup($db);
        $products = $db->quoteIdentifier('zfproducts');
        $product_name = $db->quoteIdentifier('product_name');
        /*
         * it is important not to delimit the pname identifier
         * in the following query
         */
        $sql = "SELECT $product_name AS case_folded_identifier FROM $products";
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
        $keys = array_keys($result[0]);
        return $keys[0];
    }

    /**
     * Test the connection's CASE_FOLDING option.
     * Case: Zend_Db::CASE_NATURAL
     */
    public function testAdapterOptionCaseFoldingNatural()
    {
        $natural = $this->_testAdapterOptionCaseFoldingCommon(Zend_Db::CASE_NATURAL);
        $expected = $this->_testAdapterOptionCaseFoldingNaturalIdentifier();
        $this->assertEquals($natural, $expected, 'Natural case does not match');
    }

    /**
     * Test the connection's CASE_FOLDING option.
     * Case: Zend_Db::CASE_UPPER
     */
    public function testAdapterOptionCaseFoldingUpper()
    {
        $upper = $this->_testAdapterOptionCaseFoldingCommon(Zend_Db::CASE_UPPER);
        $expected = strtoupper($this->_testAdapterOptionCaseFoldingNaturalIdentifier());
        $this->assertEquals($upper, $expected, 'Upper case does not match');
    }

    /**
     * Test the connection's CASE_FOLDING option.
     * Case: Zend_Db::CASE_LOWER
     */
    public function testAdapterOptionCaseFoldingLower()
    {
        $lower = $this->_testAdapterOptionCaseFoldingCommon(Zend_Db::CASE_LOWER);
        $expected = strtolower($this->_testAdapterOptionCaseFoldingNaturalIdentifier());
        $this->assertEquals($lower, $expected, 'Lower case does not match');
    }

    /**
     * Test the connection's CASE_FOLDING option.
     * Case: invalid value throws exception
     */
    public function testAdapterOptionCaseFoldingInvalidException()
    {
        try {
            $lower = $this->_testAdapterOptionCaseFoldingCommon(-999);
            $this->fail('Expected exception not thrown');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Adapter_Exception', $e,
                'Expecting object of type Zend_Db_Adapter_Exception, got '.get_class($e));
            $this->assertEquals("Case must be one of the following constants: Zend_Db::CASE_NATURAL, Zend_Db::CASE_LOWER, Zend_Db::CASE_UPPER", $e->getMessage());
        }
    }

    /**
     * Test that we can query twice in a row.
     * That is, a query doesn't leave the adapter in an unworking state.
     * See bug ZF-1778.
     */
    public function testAdapterQueryAfterUnclosedQuery()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order('product_id');

        $stmt1 = $this->_db->query($select);
        $result1 = $stmt1->fetchAll(Zend_Db::FETCH_NUM);

        $stmt1 = $this->_db->query($select);
        $result2 = $stmt1->fetchAll(Zend_Db::FETCH_NUM);

        $this->assertEquals($result1, $result2);
    }

    /**
     * Ensures that query() throws an exception when given a bogus query
     *
     * @return void
     */
    public function testAdapterQueryBogus()
    {
        try {
            $this->_db->query('Bogus query');
            $this->fail('Expected exception not thrown');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
        }
    }

    /**
     * Ensures that query() throws an exception when given a bogus table
     *
     * @return void
     */
    public function testAdapterQueryBogusTable()
    {
        try {
            $this->_db->query('SELECT * FROM BogusTable');
            $this->fail('Expected exception not thrown');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
        }
    }

    /**
     * Ensures that query() provides expected behavior when returning no results
     *
     * @return void
     */
    public function testAdapterQueryResultsNone()
    {
        $stmt = $this->_db->query('SELECT * FROM ' . $this->_db->quoteIdentifier('zfbugs') . ' WHERE '
            . $this->_db->quoteIdentifier('bug_id') . ' = -1');

        $this->assertTrue(is_object($stmt),
            'Expected query() to return object; got ' . gettype($stmt));

        $this->assertType('Zend_Db_Statement_Interface', $stmt,
            'Expected query() to return Zend_Db_Statement or PDOStatement; got ' . get_class($stmt));

        $this->assertEquals(0, $count = count($stmt->fetchAll()),
            "Expected fetchAll() to return zero rows; got $count");
    }

    /**
     * Test that quote() accepts a string and returns
     * a quoted string.
     */
    public function testAdapterQuote()
    {
        $string = 'String without quotes';
        $value = $this->_db->quote($string);
        $this->assertEquals("'String without quotes'", $value);
    }

    /**
     * Test that quote() takes an array and returns
     * an imploded string of comma-separated, quoted elements.
     */
    public function testAdapterQuoteArray()
    {
        $array = array("it's", 'all', 'right!');
        $value = $this->_db->quote($array);
        $this->assertEquals("'it\\'s', 'all', 'right!'", $value);
    }

    /**
     * test that quote() accepts a Zend_Db_Expr
     * and returns the string representation,
     * with no quoting applied.
     */
    public function testAdapterQuoteDbExpr()
    {
        $string = 'String with "`\' quotes';
        $expr = new Zend_Db_Expr($string);
        $value = $this->_db->quote($expr);
        $this->assertEquals($string, $value);
    }

    /**
     * test that quote() accepts a string containing
     * digits and returns an unquoted string.
     */
    public function testAdapterQuoteDigitString()
    {
        $string = '123';
        $value = $this->_db->quote($string);
        $this->assertEquals("'123'", $value);
    }

    /**
     * test that quote() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteDoubleQuote()
    {
        $string = 'St John"s Wort';
        $value = $this->_db->quote($string);
        $this->assertEquals("'St John\\\"s Wort'", $value);
    }

    /**
     * test that quote() accepts an integer and
     * returns an unquoted integer.
     */
    public function testAdapterQuoteInteger()
    {
        $int = 123;
        $value = $this->_db->quote($int);
        $this->assertEquals(123, $value);
    }

    /**
     * test that quote() accepts an array and returns
     * an imploded string of unquoted elements
     */
    public function testAdapterQuoteIntegerArray()
    {
        $array = array(1,'2',3);
        $value = $this->_db->quote($array);
        $this->assertEquals("1, '2', 3", $value);
    }

    /**
     * test that quote() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteSingleQuote()
    {
        $string = "St John's Wort";
        $value = $this->_db->quote($string);
        $this->assertEquals("'St John\'s Wort'", $value);
    }

    /**
     * test that quoteColumnAs() accepts a string
     * and an alias, and returns each as delimited
     * identifiers, with 'AS' in between.
     */
    public function testAdapterQuoteColumnAs()
    {
        $string = "foo";
        $alias = "bar";
        $value = $this->_db->quoteColumnAs($string, $alias);
        $this->assertEquals('"foo" AS "bar"', $value);
    }

    /**
     * test that quoteColumnAs() accepts a string
     * and an alias, but ignores the alias if it is
     * the same as the base identifier in the string.
     */
    public function testAdapterQuoteColumnAsSameString()
    {
        $string = 'foo.bar';
        $alias = 'bar';
        $value = $this->_db->quoteColumnAs($string, $alias);
        $this->assertEquals('"foo"."bar"', $value);
    }

    /**
     * test that quoteIdentifier() accepts a string
     * and returns a delimited identifier.
     */
    public function testAdapterQuoteIdentifier()
    {
        $string = 'table_name';
        $value = $this->_db->quoteIdentifier($string);
        $this->assertEquals('"table_name"', $value);
    }

    /**
     * test that quoteIdentifier() accepts an array
     * and returns a qualified delimited identifier.
     */
    public function testAdapterQuoteIdentifierArray()
    {
        $array = array('foo', 'bar');
        $value = $this->_db->quoteIdentifier($array);
        $this->assertEquals('"foo"."bar"', $value);
    }

    /**
     * test that quoteIdentifier() accepts an array
     * containing a Zend_Db_Expr, and returns strings
     * as delimited identifiers, and Exprs as unquoted.
     */
    public function testAdapterQuoteIdentifierArrayDbExpr()
    {
        $expr = new Zend_Db_Expr('*');
        $array = array('foo', $expr);
        $value = $this->_db->quoteIdentifier($array);
        $this->assertEquals('"foo".*', $value);
    }

    /**
     * test that quoteIdentifier() accepts a Zend_Db_Expr
     * and returns the string representation,
     * with no quoting applied.
     */
    public function testAdapterQuoteIdentifierDbExpr()
    {
        $string = 'String with "`\' quotes';
        $expr = new Zend_Db_Expr($string);
        $value = $this->_db->quoteIdentifier($expr);
        $this->assertEquals($string, $value);
    }

    /**
     * test that quoteIdentifer() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteIdentifierDoubleQuote()
    {
        $string = 'table_"_name';
        $value = $this->_db->quoteIdentifier($string);
        $this->assertEquals('"table_""_name"', $value);
    }

    /**
     * test that quoteIdentifer() accepts an integer
     * and returns a delimited identifier as with a string.
     */
    public function testAdapterQuoteIdentifierInteger()
    {
        $int = 123;
        $value = $this->_db->quoteIdentifier($int);
        $this->assertEquals('"123"', $value);
    }

    /**
     * test that quoteIdentifier() accepts a string
     * containing a dot (".") character, splits the
     * string, quotes each segment individually as
     * delimited identifers, and returns the imploded
     * string.
     */
    public function testAdapterQuoteIdentifierQualified()
    {
        $string = 'table.column';
        $value = $this->_db->quoteIdentifier($string);
        $this->assertEquals('"table"."column"', $value);
    }

    /**
     * test that quoteIdentifer() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteIdentifierSingleQuote()
    {
        $string = "table_'_name";
        $value = $this->_db->quoteIdentifier($string);
        $this->assertEquals('"table_\'_name"', $value);
    }

    /**
     * test that quoteTableAs() accepts a string and an alias,
     * and returns each as delimited identifiers.
     * Most RDBMS want an 'AS' in between.
     */
    public function testAdapterQuoteTableAs()
    {
        $string = "foo";
        $alias = "bar";
        $value = $this->_db->quoteTableAs($string, $alias);
        $this->assertEquals('"foo" AS "bar"', $value);
    }

    /**
     * test that quoteInto() accepts a Zend_Db_Expr
     * and returns the string representation,
     * with no quoting applied.
     */
    public function testAdapterQuoteIntoDbExpr()
    {
        $string = 'id=?';
        $expr = new Zend_Db_Expr('CURDATE()');
        $value = $this->_db->quoteInto($string, $expr);
        $this->assertEquals("id=CURDATE()", $value);
    }

    /**
     * test that quoteInto() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteIntoDoubleQuote()
    {
        $string = 'id=?';
        $param = 'St John"s Wort';
        $value = $this->_db->quoteInto($string, $param);
        $this->assertEquals("id='St John\\\"s Wort'", $value);
    }

    /**
     * test that quoteInto() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteIntoSingleQuote()
    {
        $string = 'id = ?';
        $param = 'St John\'s Wort';
        $value = $this->_db->quoteInto($string, $param);
        $this->assertEquals("id = 'St John\\'s Wort'", $value);
    }

    protected $_numericDataTypes = array(
        Zend_Db::INT_TYPE    => Zend_Db::INT_TYPE,
        Zend_Db::BIGINT_TYPE => Zend_Db::BIGINT_TYPE,
        Zend_Db::FLOAT_TYPE  => Zend_Db::FLOAT_TYPE
    );

    public function testAdapterQuoteIntoType()
    {
        $value = $this->_db->quoteInto('foo = ?', 1234, Zend_Db::INT_TYPE);
        $this->assertType('string', $value);
        $this->assertEquals('foo = 1234', $value,
            'Incorrect quoteInto() result for INT_TYPE');

        $value = $this->_db->quoteInto('foo = ?', 1234, Zend_Db::BIGINT_TYPE);
        $this->assertType('string', $value);
        $this->assertEquals('foo = 1234', $value,
            'Incorrect quoteInto() result for BIGINT_TYPE');

        $value = $this->_db->quoteInto('foo = ?', 12.34, Zend_Db::FLOAT_TYPE);
        $this->assertType('string', $value);
        $this->assertEquals('foo = 12.340000', $value,
            'Incorrect quoteInto() result for FLOAT_TYPE');

        $value = $this->_db->quoteInto('foo = ?', 1234, 'CHAR');
        $this->assertType('string', $value);
        $this->assertEquals("foo = 1234", $value,
            'Incorrect quoteInto() result for CHAR');

        $value = $this->_db->quoteInto('foo = ?', '1234', 'CHAR');
        $this->assertType('string', $value);
        $this->assertEquals("foo = '1234'", $value,
            'Incorrect quoteInto() result for CHAR');
    }

    public function testAdapterQuoteIntoCount()
    {
        $value = $this->_db->quoteInto('foo = ? and bar = ?', 1234, null, 1);
        $this->assertType('string', $value);
        $this->assertEquals('foo = 1234 and bar = ?', $value,
            'Incorrect quoteInto() result for count');
    }

    public function testAdapterQuoteTypeInt()
    {
        foreach ($this->_numericDataTypes as $typeName => $type) {
            if ($type != 0) {
                continue;
            }

            // test int value
            $value = $this->_db->quote(1234, $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('1234', $value,
                "Incorrect quote() INT_TYPE result for int");

            // test int string
            $value = $this->_db->quote('1234', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('1234', $value,
                "Incorrect quote() INT_TYPE result for int string");

            // test int string with + sign
            $value = $this->_db->quote('+1234', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('1234', $value,
                "Incorrect quote() INT_TYPE result for int string with + sign");

            // test int string with - sign
            $value = $this->_db->quote('-1234', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('-1234', $value,
                "Incorrect quote() INT_TYPE result for int string with - sign");

            // test int string with non-digit text
            $value = $this->_db->quote('1234abcd', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('1234', $value,
                "Incorrect quote() INT_TYPE result for int string with non-digit text");

            // test non-digit test; it should return 0
            $value = $this->_db->quote('abcd', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('0', $value,
                "Incorrect quote() INT_TYPE result for non-digit string");

            // test Zend_Db_Expr is passed through literally
            $value = $this->_db->quote(new Zend_Db_Expr('1+2+3'), $typeName);
            $this->assertType('string', $value);
            $this->assertEquals("1+2+3", $value,
                "Incorrect quote() INT_TYPE result for Zend_Db_Expr");
        }
    }

    public function testAdapterQuoteTypeBigInt()
    {
        foreach ($this->_numericDataTypes as $typeName => $type) {
            if ($type != 1) {
                continue;
            }

            // test int value
            $value = $this->_db->quote(1234, $typeName);
            $this->assertType('string', $value);
            $this->assertEquals(1234, $value,
                "Incorrect quote() BIGINT_TYPE result for int");

            // test int string
            $value = $this->_db->quote('1234', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('1234', $value,
                "Incorrect quote() BIGINT_TYPE result for int string");

            // test int string with + sign
            $value = $this->_db->quote('+1234', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('+1234', $value,
                "Incorrect quote() BIGINT_TYPE result for int string");

            // test int string with - sign
            $value = $this->_db->quote('-1234', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('-1234', $value,
                "Incorrect quote() BIGINT_TYPE result for int string");

            // test int string with positive exponent
            $value = $this->_db->quote('1234e15', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('1234e15', $value,
                "Incorrect quote() BIGINT_TYPE result for int string");

            $value = $this->_db->quote('1234e+15', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('1234e+15', $value,
                "Incorrect quote() BIGINT_TYPE result for int string");

            // test int value with negative exponent
            $value = $this->_db->quote('1234e-15', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('1234e-15', $value,
                "Incorrect quote() BIGINT_TYPE result for int string");

            // test bigint value
            $value = $this->_db->quote('2200000000', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('2200000000', $value,
                "Incorrect quote() BIGINT_TYPE result for big int string");

            // test octal value with leading zeroes
            $value = $this->_db->quote('020310253000', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('020310253000', $value,
                "Incorrect quote() BIGINT_TYPE result for big octal int string with leading zeroes");

            // test decimal value with leading zeroes
            $value = $this->_db->quote('09091', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('09091', $value,
                "Incorrect quote() BIGINT_TYPE result for decimal int string with leading zeroes");

            // test hex value with ODBC-style notation
            $value = $this->_db->quote('0x83215600', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('0x83215600', $value,
                "Incorrect quote() BIGINT_TYPE result for big hex int string");

            // test non-numeric value; it should be zero
            $value = $this->_db->quote('abcd', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('0', $value,
                "Incorrect quote() BIGINT_TYPE result for non-digt string");

            // test Zend_Db_Expr is passed through literally
            $value = $this->_db->quote(new Zend_Db_Expr('1+2+3'), $typeName);
            $this->assertType('string', $value);
            $this->assertEquals("1+2+3", $value,
                "Incorrect quote() BIGINT_TYPE result for Zend_Db_Expr");
        }
    }

    public function testAdapterQuoteTypeFloat()
    {
        foreach ($this->_numericDataTypes as $typeName => $type) {
            if ($type != 2) {
                continue;
            }

            $value = $this->_db->quote(12.34, $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('12.34', $value,
                "Incorrect quote() FLOAT_TYPE result");

            $value = $this->_db->quote('12.34', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('12.34', $value,
                "Incorrect quote() FLOAT_TYPE result");

            $value = $this->_db->quote('+12.34', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('12.34', $value,
                "Incorrect quote() FLOAT_TYPE result");

            $value = $this->_db->quote('-12.34', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('-12.34', $value,
                "Incorrect quote() FLOAT_TYPE result");

            $value = $this->_db->quote('12.34abcd', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('12.34', $value,
                "Incorrect quote() FLOAT_TYPE result");

            $value = $this->_db->quote('abcd', $typeName);
            $this->assertType('string', $value);
            $this->assertEquals('0', $value,
                "Incorrect quote() FLOAT_TYPE result");

            $value = $this->_db->quote(new Zend_Db_Expr('1+2+3'), $typeName);
            $this->assertType('string', $value);
            $this->assertEquals("1+2+3", $value,
                "Incorrect quote() FLOAT_TYPE result");
        }
    }

    public function testAdapterQuoteTypeNonNumeric()
    {
        $value = $this->_db->quote(1234, 'CHAR');
        $this->assertType('integer', $value);
        $this->assertEquals(1234, $value,
            "Incorrect quote() CHAR type result");

        $value = $this->_db->quote('1234', 'CHAR');
        $this->assertType('string', $value);
        $this->assertEquals("'1234'", $value,
            "Incorrect quote() CHAR type result");

        $value = $this->_db->quote('1234abcd', 'CHAR');
        $this->assertType('string', $value);
        $this->assertEquals("'1234abcd'", $value,
            "Incorrect quote() CHAR type result");

        $value = $this->_db->quote('1234abcd56', 'CHAR');
        $this->assertType('string', $value);
        $this->assertEquals("'1234abcd56'", $value,
            "Incorrect quote() CHAR type result");

        // test Zend_Db_Expr is passed through literally
        $value = $this->_db->quote(new Zend_Db_Expr('1+2+3'), 'CHAR');
        $this->assertType('string', $value);
        $this->assertEquals("1+2+3", $value,
            "Incorrect quote() CHAR type result for Zend_Db_Expr");
    }

    /**
     * Ensures that the character sequence ":0'" is handled properly
     *
     * @link   http://framework.zend.com/issues/browse/ZF-2059
     * @return void
     */
    public function testZF2059()
    {
        $value       = ':0\'';
        $valueQuoted = $this->_db->quote($value);
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $bug_status = $this->_db->quoteIdentifier('bug_status');
        $sql = "SELECT $bug_id FROM $bugs WHERE $bug_status != " . $valueQuoted;
        $results = $this->_db->fetchAll($sql);
        $this->assertEquals(4, count($results));
    }

    public function testAdapterSetFetchMode()
    {
        $modes = array(
            Zend_Db::FETCH_ASSOC,
            Zend_Db::FETCH_BOTH,
            Zend_Db::FETCH_NUM,
            Zend_Db::FETCH_OBJ
        );
        foreach ($modes as $mode) {
            $this->_db->setFetchMode($mode);
            $this->assertEquals($mode, $this->_db->getFetchMode());
        }
    }

    public function testAdapterSetFetchModeInvalidException()
    {
        try {
            $this->_db->setFetchMode(-999);
            $this->fail('Expected exception not thrown');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Adapter_Exception', $e,
                'Expecting object of type Zend_Db_Adapter_Exception, got '.get_class($e));
            $this->assertEquals("Invalid fetch mode '-999' specified", $e->getMessage());
        }
    }

    public function testAdapterTransactionAutoCommit()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        // use our default connection as the Connection1
        $dbConnection1 = $this->_db;

        // create a second connection to the same database
        $dbConnection2 = Zend_Db::factory($this->getDriver(), $this->_util->getParams());
        $dbConnection2->getConnection();

        // notice the number of rows in connection 2
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(4, $count);

        // delete a row in connection 1
        $rowsAffected = $dbConnection1->delete(
            'zfbugs',
            "$bug_id = 1"
        );
        $this->assertEquals(1, $rowsAffected);

        // we should see the results in connection 2 immediately
        // after the DELETE executes, because it's autocommit
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(3, $count);
    }

    public function testAdapterTransactionCommit()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        // use our default connection as the Connection1
        $dbConnection1 = $this->_db;

        // create a second connection to the same database
        $dbConnection2 = Zend_Db::factory($this->getDriver(), $this->_util->getParams());
        $dbConnection2->getConnection();

        // notice the number of rows in connection 2
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(4, $count, 'Expecting to see 4 rows in bugs table (step 1)');

        // start an explicit transaction in connection 1
        $dbConnection1->beginTransaction();

        // delete a row in connection 1
        $rowsAffected = $dbConnection1->delete(
            'zfbugs',
            "$bug_id = 1"
        );
        $this->assertEquals(1, $rowsAffected);

        // we should still see all rows in connection 2
        // because the DELETE has not been committed yet
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(4, $count, 'Expecting to still see 4 rows in bugs table (step 2); perhaps Adapter is still in autocommit mode?');

        // commit the DELETE
        $dbConnection1->commit();

        // now we should see one fewer rows in connection 2
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(3, $count, 'Expecting to see 3 rows in bugs table after DELETE (step 3)');

        // delete another row in connection 1
        $rowsAffected = $dbConnection1->delete(
            'zfbugs',
            "$bug_id = 2"
        );
        $this->assertEquals(1, $rowsAffected);

        // we should see results immediately, because
        // the db connection returns to auto-commit mode
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(2, $count);
    }

    public function testAdapterTransactionRollback()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        // use our default connection as the Connection1
        $dbConnection1 = $this->_db;

        // create a second connection to the same database
        $dbConnection2 = Zend_Db::factory($this->getDriver(), $this->_util->getParams());
        $dbConnection2->getConnection();

        // notice the number of rows in connection 2
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(4, $count, 'Expecting to see 4 rows in bugs table (step 1)');

        // start an explicit transaction in connection 1
        $dbConnection1->beginTransaction();

        // delete a row in connection 1
        $rowsAffected = $dbConnection1->delete(
            'zfbugs',
            "$bug_id = 1"
        );
        $this->assertEquals(1, $rowsAffected);

        // we should still see all rows in connection 2
        // because the DELETE has not been committed yet
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(4, $count, 'Expecting to still see 4 rows in bugs table (step 2); perhaps Adapter is still in autocommit mode?');

        // rollback the DELETE
        $dbConnection1->rollback();

        // now we should see the same number of rows
        // because the DELETE was rolled back
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(4, $count, 'Expecting to still see 4 rows in bugs table after DELETE is rolled back (step 3)');

        // delete another row in connection 1
        $rowsAffected = $dbConnection1->delete(
            'zfbugs',
            "$bug_id = 2"
        );
        $this->assertEquals(1, $rowsAffected);

        // we should see results immediately, because
        // the db connection returns to auto-commit mode
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(3, $count, 'Expecting to see 3 rows in bugs table after DELETE (step 4)');
    }

    /**
     * Test the Adapter's update() method.
     * Update a single row and verify that the change was made.
     * Attempt to update a row that does not exist, and verify
     * that no change was made.
     */
    public function testAdapterUpdate()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        // Test that we can change the values in
        // an existing row.
        $rowsAffected = $this->_db->update(
            'zfproducts',
            array('product_name' => 'Vista'),
            "$product_id = 1"
        );
        $this->assertEquals(1, $rowsAffected);

        // Query the row to see if we have the new values.
        $select = $this->_db->select();
        $select->from('zfproducts');
        $select->where("$product_id = 1");
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();

        $this->assertEquals(1, $result[0]['product_id']);
        $this->assertEquals('Vista', $result[0]['product_name']);

        // Test that update affects no rows if the WHERE
        // clause matches none.
        $rowsAffected = $this->_db->update(
            'zfproducts',
            array('product_name' => 'Vista'),
            "$product_id = 327"
        );
        $this->assertEquals(0, $rowsAffected);
    }

    public function testAdapterUpdateSetDbExpr()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $bug_status = $this->_db->quoteIdentifier('bug_status');

        $expr = new Zend_Db_Expr("UPPER('setExpr')");
        $rowsAffected = $this->_db->update(
            'zfbugs',
            array('bug_status' => $expr),
            "$bug_id = 1"
        );
        $this->assertEquals(1, $rowsAffected);

        $value = $this->_db->fetchOne("SELECT $bug_status FROM $bugs WHERE $bug_id = 1");
        $this->assertEquals('SETEXPR', $value);
    }

    public function testAdapterUpdateWhereArray()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $bug_status = $this->_db->quoteIdentifier('bug_status');

        $rowsAffected = $this->_db->update(
            'zfbugs',
            array('bug_status' => 'ARRAY'),
            array("$bug_id = 1", "$bug_status = 'NEW'")
        );
        $this->assertEquals(1, $rowsAffected);

        $value = $this->_db->fetchOne("SELECT $bug_status FROM $bugs WHERE $bug_id = 1");
        $this->assertEquals('ARRAY', $value);
    }

    /**
     * @group ZF-1726
     */
    public function testAdapterUpdateWhereArrayWithVariable()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $bug_status = $this->_db->quoteIdentifier('bug_status');

        $rowsAffected = $this->_db->update(
            'zfbugs',
            array('bug_status' => 'ARRAY'),
            array("$bug_id = ?" => 1, "$bug_status = ?" => 'NEW')
        );
        $this->assertEquals(1, $rowsAffected);

        $value = $this->_db->fetchOne("SELECT $bug_status FROM $bugs WHERE $bug_id = 1");
        $this->assertEquals('ARRAY', $value);
    }

    public function testAdapterUpdateWhereDbExpr()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $bug_status = $this->_db->quoteIdentifier('bug_status');

        $whereExpr = new Zend_Db_Expr("$bug_id = 1");

        $rowsAffected = $this->_db->update(
            'zfbugs',
            array('bug_status' => 'DBEXPR'),
            $whereExpr
        );
        $this->assertEquals(1, $rowsAffected);

        $value = $this->_db->fetchOne("SELECT $bug_status FROM $bugs WHERE $bug_id = 1");
        $this->assertEquals('DBEXPR', $value);
    }

    public function testAdapterUpdateEmptyWhere()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_status = $this->_db->quoteIdentifier('bug_status');

        $rowsAffected = $this->_db->update(
            'zfbugs',
            array('bug_status' => 'EMPTY')
            // intentionally no where clause
        );
        $this->assertEquals(4, $rowsAffected);

        $value = $this->_db->fetchCol("SELECT $bug_status FROM $bugs");
        $this->assertEquals(array_fill(0, 4, 'EMPTY'), $value);
    }

    /**
     * @group ZF-8597
     * Oracle is limited to 30 characters for an identifier
     */
    public function testAdapterUpdateWithLongColumnIdentifier()
    {
        // create test table using no identifier quoting
        $this->_util->createTable('zf_longidentifier', array(
            'id'    => 'INTEGER NOT NULL',
            'veryveryveryverylongidentifier' => 'INTEGER NOT NULL'
        ));
        $tableName = $this->_util->getTableName('zf_longidentifier');

        // insert into the table
        $this->_db->insert($tableName, array(
            'id' => 1,
            'veryveryveryverylongidentifier' => 2
        ));

        //try to update
        $this->_db->update($tableName,
                           array('veryveryveryverylongidentifier' => 3),
                           array($this->_db->quoteIdentifier('id') . ' = 1'));

        // check if the row was inserted as expected
        $select = $this->_db->select()->from($tableName, array('id', 'veryveryveryverylongidentifier'));

        $stmt = $this->_db->query($select);
        $fetched = $stmt->fetchAll(Zend_Db::FETCH_NUM);
        $a = array(
            0 => array(0 => 1, 1 => 3)
        );
        $this->assertEquals($a, $fetched,
            'result of query not as expected');

        // clean up
        unset($stmt);
        $this->_util->dropTable($tableName);
    }

    protected function _testAdapterAlternateStatement($stmtClass)
    {
        $ip = get_include_path();
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files';
        $newIp = $dir . PATH_SEPARATOR . $ip;
        set_include_path($newIp);

        $params = $this->_util->getParams();

        $params['options'] = array(
            Zend_Db::AUTO_QUOTE_IDENTIFIERS => false
        );
        $db = Zend_Db::factory($this->getDriver(), $params);
        $db->getConnection();
        $db->setStatementClass($stmtClass);

        $currentStmtClass = $db->getStatementClass();
        $this->assertEquals($stmtClass, $currentStmtClass);

        $bugs = $this->_db->quoteIdentifier('zfbugs');

        $stmt = $db->prepare("SELECT COUNT(*) FROM $bugs");

        $this->assertType($stmtClass, $stmt,
            'Expecting object of type ' . $stmtClass . ', got ' . get_class($stmt));
    }

    public function testAdapterSetCustomProfiler()
    {
        $profilerOptions = array(
            'enabled'  => true,
            'class'    => 'stdClass',
            'instance' => new stdClass()
        );

        try {
            $this->_db->setProfiler($profilerOptions);
            $this->fail('Expected to catch Zend_Db_Profiler_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Profiler_Exception', $e);
            $this->assertEquals('Class stdClass does not extend Zend_Db_Profiler', $e->getMessage());
        }
    }

    /**
     * @group ZF-5099
     */
    public function testAdapterGetServerVersion()
    {
        $version = $this->_db->getServerVersion();
        $this->assertNotNull($version);
        $this->assertTrue(version_compare($version, '1.0.0', '>'));
        $this->assertTrue(version_compare($version, '99.0.0', '<'));
    }

    public function testAdapterGetConnection()
    {
        $this->_db->getConnection();
        $this->assertTrue($this->_db->isConnected());
    }

    /**
     * @group ZF-5050
     */
    public function testAdapterCloseConnection()
    {
        $this->_db->closeConnection();
        $this->assertFalse($this->_db->isConnected());
        // Double closing must be without any errors
        $this->_db->closeConnection();
        $this->assertFalse($this->_db->isConnected());
    }

    /**
     * @group ZF-5146
     */
    public function testAdapterReadClobFetchAll()
    {
        $documents = $this->_db->quoteIdentifier('zfdocuments');
        $document_id = $this->_db->quoteIdentifier('doc_id');
        $value = $this->_db->fetchAll("SELECT * FROM $documents WHERE $document_id = 1");
        $expected = 'this is the clob that never ends...'.
                    'this is the clob that never ends...'.
                    'this is the clob that never ends...';
        $this->assertEquals($expected, $value[0]['doc_clob']);
    }

    /**
     * @group ZF-5146
     */
    public function testAdapterReadClobFetchRow()
    {
        $documents = $this->_db->quoteIdentifier('zfdocuments');
        $document_id = $this->_db->quoteIdentifier('doc_id');
        $value = $this->_db->fetchRow("SELECT * FROM $documents WHERE $document_id = 1");
        $expected = 'this is the clob that never ends...'.
                    'this is the clob that never ends...'.
                    'this is the clob that never ends...';
        $this->assertEquals($expected, $value['doc_clob']);
    }

    /**
     * @group ZF-5146
     */
    public function testAdapterReadClobFetchAssoc()
    {
        $documents = $this->_db->quoteIdentifier('zfdocuments');
        $document_id = $this->_db->quoteIdentifier('doc_id');
        $value = $this->_db->fetchAssoc("SELECT * FROM $documents WHERE $document_id = 1");
        $expected = 'this is the clob that never ends...'.
                    'this is the clob that never ends...'.
                    'this is the clob that never ends...';
        $this->assertEquals($expected, $value[1]['doc_clob']);
    }

    /**
     * @group ZF-5146
     */
    public function testAdapterReadClobFetchCol()
    {
        $documents = $this->_db->quoteIdentifier('zfdocuments');
        $document_id = $this->_db->quoteIdentifier('doc_id');
        $document_clob = $this->_db->quoteIdentifier('doc_clob');
        $value = $this->_db->fetchCol("SELECT $document_clob FROM $documents WHERE $document_id = 1");
        $expected = 'this is the clob that never ends...'.
                    'this is the clob that never ends...'.
                    'this is the clob that never ends...';
        $this->assertEquals($expected, $value[0]);
    }

    /**
     * @group ZF-5146
     */
    public function testAdapterReadClobFetchOne()
    {
        $documents = $this->_db->quoteIdentifier('zfdocuments');
        $document_id = $this->_db->quoteIdentifier('doc_id');
        $document_clob = $this->_db->quoteIdentifier('doc_clob');
        $value = $this->_db->fetchOne("SELECT $document_clob FROM $documents WHERE $document_id = 1");
        $expected = 'this is the clob that never ends...'.
                    'this is the clob that never ends...'.
                    'this is the clob that never ends...';
        $this->assertEquals($expected, $value);
    }

    public function testAdapterSerializationIsOkByDefault()
    {
        $serialized = serialize($this->_db);
        $this->assertType('string', $serialized);
        $this->assertThat(unserialize($serialized), new PHPUnit_Framework_Constraint_IsInstanceOf('Zend_Db_Adapter_Abstract'));
    }

    public function testAdapterSerializationFailsWhenNotAllowedToBeSerialized()
    {
        $params = $this->_util->getParams();
        $params['options'] = array(
            Zend_Db::ALLOW_SERIALIZATION => false
        );
        $db = Zend_Db::factory($this->getDriver(), $params);
        $this->setExpectedException('Zend_Db_Adapter_Exception');
        $serialized = serialize($db);
    }

    public function testAdapterUnSerializationAutoReconnection()
    {
        $serialized = serialize($this->_db);
        $db = unserialize($serialized);
        $this->assertFalse($db->isConnected());

        $params = $this->_util->getParams();
        $params['options'] = array(
            Zend_Db::AUTO_RECONNECT_ON_UNSERIALIZE => true
        );
        $db = Zend_Db::factory($this->getDriver(), $params);
        $db = unserialize(serialize($db));
        $this->assertTrue($db->isConnected());
    }

    /**
     * @group ZF-1541
     */
    public function testCharacterSetUtf8()
    {
        // Create a new adapter
        $params = $this->_util->getParams();

        $params['charset'] = 'utf8';

        $db = Zend_Db::factory($this->getDriver(), $params);

         // create a new util object, with the new db adapter
        $driver = $this->getDriver();
        $utilClass = "Zend_Db_TestUtil_{$driver}";
        $util = new $utilClass();
        $util->setAdapter($db);

        // create test table using no identifier quoting
        $util->createTable('charsetutf8', array(
            'id'    => 'IDENTITY',
            'stuff' => 'VARCHAR(32)'
        ));
        $tableName = $this->_util->getTableName('charsetutf8');

        // insert into the table
        $numRows = $db->insert($tableName, array(
            'id'    => 1,
            'stuff' => 'äöüß'
        ));

        // check if the row was inserted as expected
        $select = $db->select()->from($tableName, array('id', 'stuff'));

        $stmt = $db->query($select);
        $fetched = $stmt->fetchAll(Zend_Db::FETCH_NUM);
        $a = array(
            0 => array(0 => 1, 1 => 'äöüß')
        );
        $this->assertEquals($a, $fetched,
            'result of query not as expected');

        // clean up
        unset($stmt);
        $util->dropTable($tableName);
    }

    /**
     * @group ZF-7737
     */
    public function testQuoteIntoReplacesPlaceholderAtFirstCharacterWhenCountIsNotNull()
    {
        $quotedString = $this->_db->quoteInto('? = bar', 'foo', NULL, 1);
        $this->assertEquals("'foo' = bar", $quotedString);
    }

    /**
     * @group ZF-8399
     */
    public function testLongQueryWithTextField()
    {
        // create test table using no identifier quoting
        $this->_util->createTable('zf_longquery', array(
            'id'    => 'INTEGER NOT NULL',
            'stuff' => 'TEXT NOT NULL'
        ));
        $tableName = $this->_util->getTableName('zf_longquery');

        // insert into the table
        $longValue = str_repeat('x', 4000);
        $numRows = $this->_db->insert($tableName, array(
            'id' => 1,
            'stuff' => $longValue
        ));

        $quotedTableName = $this->_db->quoteIdentifier('zf_longquery');
        $sql = "INSERT INTO $quotedTableName VALUES (2, '$longValue')";
        $this->_db->query($sql);

        // check if the row was inserted as expected
        $select = $this->_db->select()->from($tableName, array('id', 'stuff'));

        $stmt = $this->_db->query($select);
        $fetched = $stmt->fetchAll(Zend_Db::FETCH_NUM);
        $a = array(
            0 => array(0 => 1, 1 => $longValue),
            1 => array(0 => 2, 1 => $longValue)
        );
        $this->assertEquals($a, $fetched,
            'result of query not as expected');

        // clean up
        unset($stmt);
        $this->_util->dropTable($tableName);
    }
}
