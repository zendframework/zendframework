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
 * @see Zend_Db_Adapter_TestCommon
 */
require_once 'Zend/Db/Adapter/TestCommon.php';

/**
 * @see Zend_Db_Adapter_Sqlsrv
 */
require_once 'Zend/Db/Adapter/Sqlsrv.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_SqlsrvTest extends Zend_Db_Adapter_TestCommon
{
    protected $_numericDataTypes = array(
        Zend_Db::INT_TYPE    => Zend_Db::INT_TYPE,
        Zend_Db::BIGINT_TYPE => Zend_Db::BIGINT_TYPE,
        Zend_Db::FLOAT_TYPE  => Zend_Db::FLOAT_TYPE,
        'INT'                => Zend_Db::INT_TYPE,
        'SMALLINT'           => Zend_Db::INT_TYPE,
        'TINYINT'            => Zend_Db::INT_TYPE,
        'BIGINT'             => Zend_Db::BIGINT_TYPE,
        'DECIMAL'            => Zend_Db::FLOAT_TYPE,
        'FLOAT'              => Zend_Db::FLOAT_TYPE,
        'MONEY'              => Zend_Db::FLOAT_TYPE,
        'NUMERIC'            => Zend_Db::FLOAT_TYPE,
        'REAL'               => Zend_Db::FLOAT_TYPE,
        'SMALLMONEY'         => Zend_Db::FLOAT_TYPE
    );

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

        $lastInsertId = $this->_db->lastInsertId('zfbugs');
        $this->assertEquals('5', (string) $lastInsertId,
            'Expected new id to be 5, selecting by table');
    }

    /**
     * Test the Adapter's insert() method.
     * This requires providing an associative array of column=>value pairs.
     * Multiple rows are insert in one query
     */
    public function testAdapterMultipleInsert()
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

        $bugs = $this->_db->quoteIdentifier('zfbugs');

        $values = '(?, ?, ?, ?, ?, ?, ?)';

        $query = 'INSERT INTO ' . $bugs . ' VALUES ' . implode(',', array($values, $values, $values));

        $data = array();

        for ($i = 0; $i < 3; $i++) {
            foreach ($row as $value) {
                $data[] = $value;
            }
        }

        $stmt = $this->_db->query($query, $data);
        $rowsAffected = $stmt->rowCount();
        $this->assertEquals(3, $rowsAffected);
    }

    public function testAdapterDescribeTableAttributeColumn()
    {
        $desc = $this->_db->describeTable('zfproducts');

        $this->assertEquals('zfproducts',   $desc['product_name']['TABLE_NAME']);
        $this->assertEquals('product_name', $desc['product_name']['COLUMN_NAME']);
        $this->assertEquals(2,              $desc['product_name']['COLUMN_POSITION']);
        $this->assertRegExp('/varchar/i',   $desc['product_name']['DATA_TYPE']);
        $this->assertEquals('',             $desc['product_name']['DEFAULT']);
        $this->assertTrue($desc['product_name']['NULLABLE'], 'Expected product_name to be nullable');
        $this->assertNull($desc['product_name']['SCALE'], 'scale is not 0');

        // MS SQL Server reports varchar length in the PRECISION field.  Whaaa?!?
        $this->assertEquals(100, $desc['product_name']['PRECISION'], 'precision is not 100');
        $this->assertFalse($desc['product_name']['PRIMARY'], 'Expected product_name not to be a primary key');
        $this->assertNull($desc['product_name']['PRIMARY_POSITION'], 'Expected product_name to return null for PRIMARY_POSITION');
        $this->assertFalse($desc['product_name']['IDENTITY'], 'Expected product_name to return false for IDENTITY');
    }

    public function testAdapterDescribeTablePrimaryKeyColumn()
    {
        $desc = $this->_db->describeTable('zfproducts');

        $this->assertEquals('zfproducts', $desc['product_id']['TABLE_NAME']);
        $this->assertEquals('product_id', $desc['product_id']['COLUMN_NAME']);
        $this->assertEquals(1,            $desc['product_id']['COLUMN_POSITION']);
        $this->assertEquals('',           $desc['product_id']['DEFAULT']);
        $this->assertFalse($desc['product_id']['NULLABLE'], 'Expected product_id not to be nullable');
        $this->assertEquals(0,            $desc['product_id']['SCALE'], 'scale is not 0');
        $this->assertEquals(10,           $desc['product_id']['PRECISION'], 'precision is not 10');
        $this->assertTrue($desc['product_id']['PRIMARY'], 'Expected product_id to be a primary key');
        $this->assertEquals(1,            $desc['product_id']['PRIMARY_POSITION']);
    }

    /**
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
     * test that quote() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteDoubleQuote()
    {
        $string = 'St John"s Wort';
        $value  = $this->_db->quote($string);
        $this->assertEquals("'St John\"s Wort'", $value);
    }

    /**
     * test that quote() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteSingleQuote()
    {
        $string = "St John's Wort";
        $value  = $this->_db->quote($string);
        $this->assertEquals("'St John''s Wort'", $value);
    }

    /**
     * test that quoteInto() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteIntoDoubleQuote()
    {
        $string = 'id=?';
        $param  = 'St John"s Wort';
        $value  = $this->_db->quoteInto($string, $param);
        $this->assertEquals("id='St John\"s Wort'", $value);
    }

    /**
     * test that quoteInto() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteIntoSingleQuote()
    {
        $string = 'id = ?';
        $param  = 'St John\'s Wort';
        $value  = $this->_db->quoteInto($string, $param);
        $this->assertEquals("id = 'St John''s Wort'", $value);
    }

    public function testAdapterInsertSequence()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support sequences.');
    }

    public function testAdapterInsertDbExpr()
    {
        $bugs   = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $expr   = new Zend_Db_Expr('2+3');

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

        $this->_db->query("SET IDENTITY_INSERT $bugs ON");

        $rowsAffected = $this->_db->insert('zfbugs', $row);
        $this->assertEquals(1, $rowsAffected);

        $this->_db->query("SET IDENTITY_INSERT $bugs OFF");

        $value = $this->_db->fetchOne("SELECT $bug_id FROM $bugs WHERE $bug_id = 5");
        $this->assertEquals(5, $value);
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
        $driver    = $this->getDriver();
        $utilClass = "Zend_Db_TestUtil_{$driver}";
        $util      = new $utilClass();
        $util->setAdapter($db);

        // create test table using no identifier quoting
        $util->createTable('charsetutf8', array(
            'id'    => 'IDENTITY',
            'stuff' => 'VARCHAR(32)'
        ));
        $tableName = $this->_util->getTableName('charsetutf8');

        $table = $db->quoteIdentifier('charsetutf8');

        $db->query("SET IDENTITY_INSERT $table ON");

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

        $db->query("SET IDENTITY_INSERT $table OFF");

        // clean up
        unset($stmt);
        $util->dropTable($tableName);
    }

    public function testAdapterTransactionCommit()
    {
        $bugs   = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        // notice the number of rows in connection 2
        $count = $this->_db->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(4, $count, 'Expecting to see 4 rows in bugs table (step 1)');

        // start an explicit transaction in connection 1
        $this->_db->beginTransaction();

        // delete a row in connection 1
        $rowsAffected = $this->_db->delete(
            'zfbugs',
            "$bug_id = 1"
        );
        $this->assertEquals(1, $rowsAffected);

        // we should still see all rows in connection 2
        // because the DELETE has not been committed yet
        $count = $this->_db->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(3, $count, 'Expecting to still see 4 rows in bugs table (step 2); perhaps Adapter is still in autocommit mode?');

        // commit the DELETE
        $this->_db->commit();

        // now we should see one fewer rows in connection 2
        $count = $this->_db->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(3, $count, 'Expecting to see 3 rows in bugs table after DELETE (step 3)');

        // delete another row in connection 1
        $rowsAffected = $this->_db->delete(
            'zfbugs',
            "$bug_id = 2"
        );
        $this->assertEquals(1, $rowsAffected);

        // we should see results immediately, because
        // the db connection returns to auto-commit mode
        $count = $this->_db->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(2, $count);
    }

    public function testAdapterTransactionRollback()
    {
        $bugs   = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        // notice the number of rows in connection 2
        $count = $this->_db->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(4, $count, 'Expecting to see 4 rows in bugs table (step 1)');

        // start an explicit transaction in connection 1
        $this->_db->beginTransaction();

        // delete a row in connection 1
        $rowsAffected = $this->_db->delete(
            'zfbugs',
            "$bug_id = 1"
        );
        $this->assertEquals(1, $rowsAffected);

        // we should still see all rows in connection 2
        // because the DELETE has not been committed yet
        $count = $this->_db->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(3, $count, 'Expecting to still see 4 rows in bugs table (step 2); perhaps Adapter is still in autocommit mode?');

        // rollback the DELETE
        $this->_db->rollback();

        // now we should see the same number of rows
        // because the DELETE was rolled back
        $count = $this->_db->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(4, $count, 'Expecting to still see 4 rows in bugs table after DELETE is rolled back (step 3)');

        // delete another row in connection 1
        $rowsAffected = $this->_db->delete(
            'zfbugs',
            "$bug_id = 2"
        );
        $this->assertEquals(1, $rowsAffected);

        // we should see results immediately, because
        // the db connection returns to auto-commit mode
        $count = $this->_db->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(3, $count, 'Expecting to see 3 rows in bugs table after DELETE (step 4)');
    }

    public function testCanChangeIsolationLevel()
    {
        $db = $this->_db;

        // All of these should work
        $this->assertTrue($db->setTransactionIsolationLevel(SQLSRV_TXN_READ_UNCOMMITTED));
        $this->assertTrue($db->setTransactionIsolationLevel(SQLSRV_TXN_READ_COMMITTED));
        $this->assertTrue($db->setTransactionIsolationLevel(SQLSRV_TXN_REPEATABLE_READ));
        $this->assertTrue($db->setTransactionIsolationLevel(SQLSRV_TXN_SNAPSHOT));
        $this->assertTrue($db->setTransactionIsolationLevel(SQLSRV_TXN_SERIALIZABLE));

        try {
            $db->setTransactionIsolationLevel('not existing isolation level');
            $this->fail("Not existing isolation types are allowed to set");
        } catch (Zend_Db_Adapter_Sqlsrv_Exception $e) {
        }

        $this->assertTrue($db->setTransactionIsolationLevel(), "Setting to default should work by passsing null or nothing");
    }

    public function getDriver()
    {
        return 'Sqlsrv';
    }
}
