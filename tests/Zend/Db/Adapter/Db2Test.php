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
 * @version    $Id $
 */

/**
 * @see Zend_Db_Adapter_TestCommon
 */
require_once 'Zend/Db/Adapter/TestCommon.php';

/**
 * @see Zend_Db_Adapter_Db2
 */
require_once 'Zend/Db/Adapter/Db2.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Adapter
 */
class Zend_Db_Adapter_Db2Test extends Zend_Db_Adapter_TestCommon
{

    protected $_numericDataTypes = array(
        Zend_Db::INT_TYPE    => Zend_Db::INT_TYPE,
        Zend_Db::BIGINT_TYPE => Zend_Db::BIGINT_TYPE,
        Zend_Db::FLOAT_TYPE  => Zend_Db::FLOAT_TYPE,
        'INTEGER'            => Zend_Db::INT_TYPE,
        'SMALLINT'           => Zend_Db::INT_TYPE,
        'BIGINT'             => Zend_Db::BIGINT_TYPE,
        'DECIMAL'            => Zend_Db::FLOAT_TYPE,
        'NUMERIC'            => Zend_Db::FLOAT_TYPE
    );

    public function testAdapterDescribeTablePrimaryAuto()
    {
        $desc = $this->_db->describeTable('zfbugs');

        $this->assertTrue($desc['bug_id']['PRIMARY']);
        $this->assertEquals(1, $desc['bug_id']['PRIMARY_POSITION']);
        $this->assertTrue($desc['bug_id']['IDENTITY']);
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
        if (!$this->_db->isI5()) {
            $this->assertEquals(0,                   $desc['product_name']['SCALE'], 'Expected scale to be 0');
        } else {
            $this->assertNull(                   $desc['product_name']['SCALE'], 'Expected scale to be 0');
        }
        $this->assertEquals(0,                   $desc['product_name']['PRECISION'], 'Expected precision to be 0');
        $this->assertFalse(                      $desc['product_name']['PRIMARY'], 'Expected product_name not to be a primary key');
        $this->assertNull(                       $desc['product_name']['PRIMARY_POSITION'], 'Expected product_name to return null for PRIMARY_POSITION');
        $this->assertFalse(                      $desc['product_name']['IDENTITY'], 'Expected product_name to return false for IDENTITY');
    }

    public function testAdapterDescribeTablePrimaryKeyColumn()
    {
        $desc = $this->_db->describeTable('zfproducts');

        $this->assertEquals('zfproducts',        $desc['product_id']['TABLE_NAME'], 'Expected table name to be zfproducts');
        $this->assertEquals('product_id',        $desc['product_id']['COLUMN_NAME'], 'Expected column name to be product_id');
        $this->assertEquals(1,                   $desc['product_id']['COLUMN_POSITION'], 'Expected column position to be 1');
        $this->assertEquals('',                  $desc['product_id']['DEFAULT'], 'Expected default to be empty string');
        $this->assertFalse(                      $desc['product_id']['NULLABLE'], 'Expected product_id not to be nullable');
        $this->assertEquals(0,                   $desc['product_id']['SCALE'], 'Expected scale to be 0');
        $this->assertEquals(0,                   $desc['product_id']['PRECISION'], 'Expected precision to be 0');
        $this->assertTrue(                       $desc['product_id']['PRIMARY'], 'Expected product_id to be a primary key');
        $this->assertEquals(1,                   $desc['product_id']['PRIMARY_POSITION']);
    }

    /**
     * Used by _testAdapterOptionCaseFoldingNatural()
     * DB2 and Oracle return identifiers in uppercase naturally,
     * so those test suites will override this method.
     */
    protected function _testAdapterOptionCaseFoldingNaturalIdentifier()
    {
        return 'CASE_FOLDED_IDENTIFIER';
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
        if ($dbConnection2->isI5()) {
            $dbConnection2->query('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
        } else {
            $dbConnection2->query('SET ISOLATION LEVEL = UR');
        }

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

        // we should see one less row in connection 2
        // because it is doing an uncommitted read
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(3, $count, 'Expecting to see 3 rows in bugs table (step 2) because conn2 is doing an uncommitted read');

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
        if ($dbConnection2->isI5()) {
            $dbConnection2->query('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
        } else {
            $dbConnection2->query('SET ISOLATION LEVEL = UR');
        }

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

        // we should see one less row in connection 2
        // because it is doing an uncommitted read
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(3, $count, 'Expecting to see 3 rows in bugs table (step 2) because conn2 is doing an uncommitted read');

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

    public function testAdapterAlternateStatement()
    {
        $this->_testAdapterAlternateStatement('Test_Db2Statement');
    }

    /**
     * OVERRIDDEN COMMON TEST CASE
     *
     * This test case will produce a value with two internally set values,
     * autocommit = 1
     * DB2_ATTR_CASE = 0
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

        $expectedValue = array(
            'autocommit' => 1,
            'DB2_ATTR_CASE' => 0
            );
        $this->assertEquals($expectedValue, $config['driver_options']);
    }

    /**
     * OVERRIDDEN COMMON TEST CASE
     *
     * Test that quote() takes an array and returns
     * an imploded string of comma-separated, quoted elements.
     */
    public function testAdapterQuoteArray()
    {
        $array = array("it's", 'all', 'right!');
        $value = $this->_db->quote($array);
        $this->assertEquals("'it''s', 'all', 'right!'", $value);
    }

    /**
     * OVERRRIDEEN COMMON TEST CASE
     *
     * test that quote() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteDoubleQuote()
    {
        $string = 'St John"s Wort';
        $value = $this->_db->quote($string);
        $this->assertEquals("'St John\"s Wort'", $value);
    }

    /**
     * OVERRIDDEN FROM COMMON TEST CASE
     *
     * test that quote() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteSingleQuote()
    {
        $string = "St John's Wort";
        $value = $this->_db->quote($string);
        $this->assertEquals("'St John''s Wort'", $value);
    }

    /**
     * OVERRIDDEN FROM COMMON TEST CASE
     *
     * test that quoteInto() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteIntoDoubleQuote()
    {
        $string = 'id=?';
        $param = 'St John"s Wort';
        $value = $this->_db->quoteInto($string, $param);
        $this->assertEquals("id='St John\"s Wort'", $value);
    }

    /**
     * OVERRIDDEN FROM COMMON TEST CASE
     *
     * test that quoteInto() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteIntoSingleQuote()
    {
        $string = 'id = ?';
        $param = 'St John\'s Wort';
        $value = $this->_db->quoteInto($string, $param);
        $this->assertEquals("id = 'St John''s Wort'", $value);
    }

    /**
     * This is "related" to the issue.  It appears the fix for
     * describeTable is relatively untestable due to the fact that
     * its primary focus is to reduce the query time, not the result
     * set.
     *
     * @group ZF-5169
     */
    public function testAdapterSchemaOptionInListTables()
    {
        $params = $this->_util->getParams();
        unset($params['schema']);
        $connection = Zend_Db::factory($this->getDriver(), $params);
        $tableCountNoSchema = count($connection->listTables());

        $dbConfig = $this->_db->getConfig();
        if ($this->_db->isI5()) {
            if (isset($dbConfig['driver_options']['i5_lib'])) {
                $schema = $dbConfig['driver_options']['i5_lib'];
            }
        } elseif (!$this->_db->isI5()) {
            $schema = $this->_util->getSchema();
        } else {
            $this->markTestSkipped('No valid schema to test against.');
            return;
        }

        $params = $this->_util->getParams();
        $params['schema'] = $schema;
        $connection = Zend_Db::factory($this->getDriver(), $params);
        $tableCountSchema = count($connection->listTables());

        $this->assertGreaterThan(0, $tableCountNoSchema, 'Adapter without schema should produce large result');
        $this->assertGreaterThan(0, $tableCountSchema, 'Adapter with schema should produce large result');

        $this->assertTrue(($tableCountNoSchema > $tableCountSchema), 'Table count with schema provided should be less than without.');
    }

    public function getDriver()
    {
        return 'Db2';
    }

}
