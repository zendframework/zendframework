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
 * @namespace
 */
namespace ZendTest\Db\Adapter;
use Zend\Db;


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Adapter
 */
class MysqliTest extends AbstractTest
{

    protected $_numericDataTypes = array(
        Db\Db::INT_TYPE    => Db\Db::INT_TYPE,
        Db\Db::BIGINT_TYPE => Db\Db::BIGINT_TYPE,
        Db\Db::FLOAT_TYPE  => Db\Db::FLOAT_TYPE,
        'INT'                => Db\Db::INT_TYPE,
        'INTEGER'            => Db\Db::INT_TYPE,
        'MEDIUMINT'          => Db\Db::INT_TYPE,
        'SMALLINT'           => Db\Db::INT_TYPE,
        'TINYINT'            => Db\Db::INT_TYPE,
        'BIGINT'             => Db\Db::BIGINT_TYPE,
        'SERIAL'             => Db\Db::BIGINT_TYPE,
        'DEC'                => Db\Db::FLOAT_TYPE,
        'DECIMAL'            => Db\Db::FLOAT_TYPE,
        'DOUBLE'             => Db\Db::FLOAT_TYPE,
        'DOUBLE PRECISION'   => Db\Db::FLOAT_TYPE,
        'FIXED'              => Db\Db::FLOAT_TYPE,
        'FLOAT'              => Db\Db::FLOAT_TYPE
    );

    public function setup()
    {
        $this->markTestSkipped('This suite is skipped until Zend\Db can be refactored.');
    }
    
    /**
     * Test AUTO_QUOTE_IDENTIFIERS option
     * Case: Zend_Db::AUTO_QUOTE_IDENTIFIERS = true
     *
     * MySQL actually allows delimited identifiers to remain
     * case-insensitive, so this test overrides its parent.
     */
    public function testAdapterAutoQuoteIdentifiersTrue()
    {
        $params = $this->_util->getParams();

        $params['options'] = array(
            Db\Db::AUTO_QUOTE_IDENTIFIERS => true
        );
        $db = Db\Db::factory($this->getDriver(), $params);
        $db->getConnection();

        $select = $this->_db->select();
        $select->from('zfproducts');
        $stmt = $this->_db->query($select);
        $result1 = $stmt->fetchAll();
        $this->assertEquals(3, count($result1), 'Expected 3 rows in first query result');

        $this->assertEquals(1, $result1[0]['product_id']);

        $select = $this->_db->select();
        $select->from('zfproducts');
        try {
            $stmt = $this->_db->query($select);
            $result2 = $stmt->fetchAll();
            $this->assertEquals(3, count($result2), 'Expected 3 rows in second query result');
            $this->assertEquals($result1, $result2);
        } catch (\Zend\Exception $e) {
            $this->fail('exception caught where none was expected.');
        }
    }

    public function testAdapterInsertSequence()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support sequences');
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
        $this->assertEquals('`foo` AS `bar`', $value);
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
        $this->assertEquals('`foo`.`bar`', $value);
    }

    /**
     * test that quoteIdentifier() accepts a string
     * and returns a delimited identifier.
     */
    public function testAdapterQuoteIdentifier()
    {
        $value = $this->_db->quoteIdentifier('table_name');
        $this->assertEquals('`table_name`', $value);
    }

    /**
     * test that quoteIdentifier() accepts an array
     * and returns a qualified delimited identifier.
     */
    public function testAdapterQuoteIdentifierArray()
    {
        $array = array('foo', 'bar');
        $value = $this->_db->quoteIdentifier($array);
        $this->assertEquals('`foo`.`bar`', $value);
    }

    /**
     * test that quoteIdentifier() accepts an array
     * containing a Zend_Db_Expr, and returns strings
     * as delimited identifiers, and Exprs as unquoted.
     */
    public function testAdapterQuoteIdentifierArrayDbExpr()
    {
        $expr = new Db\Expr('*');
        $array = array('foo', $expr);
        $value = $this->_db->quoteIdentifier($array);
        $this->assertEquals('`foo`.*', $value);
    }

    /**
     * test that quoteIdentifer() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteIdentifierDoubleQuote()
    {
        $string = 'table_"_name';
        $value = $this->_db->quoteIdentifier($string);
        $this->assertEquals('`table_"_name`', $value);
    }

    /**
     * test that quoteIdentifer() accepts an integer
     * and returns a delimited identifier as with a string.
     */
    public function testAdapterQuoteIdentifierInteger()
    {
        $int = 123;
        $value = $this->_db->quoteIdentifier($int);
        $this->assertEquals('`123`', $value);
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
        $this->assertEquals('`table`.`column`', $value);
    }

    /**
     * test that quoteIdentifer() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteIdentifierSingleQuote()
    {
        $string = "table_'_name";
        $value = $this->_db->quoteIdentifier($string);
        $this->assertEquals('`table_\'_name`', $value);
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
        $this->assertEquals('`foo` AS `bar`', $value);
    }

    /**
     * test that describeTable() returns correct types
     * @group ZF-3624
     *
     */
    public function testAdapterDescribeTableAttributeColumnFloat()
    {
        $desc = $this->_db->describeTable('zfprice');
        $this->assertEquals('zfprice',  $desc['price']['TABLE_NAME']);
        $this->assertRegExp('/float/i', $desc['price']['DATA_TYPE']);
    }

    /**
     * Ensures that the PDO Buffered Query does not throw the error
     * 2014 General error
     *
     * @group  ZF-2101
     * @link   http://framework.zend.com/issues/browse/ZF-2101
     * @return void
     */
    public function testAdapterToEnsurePdoBufferedQueryThrowsNoError()
    {
        $params = $this->_util->getParams();
        $db = Db\Db::factory($this->getDriver(), $params);

        // Set default bound value
        $customerId = 1;

        // Stored procedure returns a single row
        $stmt = $db->prepare('CALL zf_test_procedure(?)');
        $stmt->bindParam(1, $customerId);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['product_id']);

        // Reset statement
        $stmt->closeCursor();

        // Stored procedure returns a single row
        $stmt = $db->prepare('CALL zf_test_procedure(?)');
        $stmt->bindParam(1, $customerId);
        $stmt->execute();
        $this->assertEquals(1, $result[0]['product_id']);
    }

    public function testAdapterAlternateStatement()
    {
        $this->_testAdapterAlternateStatement('Test_MysqliStatement');
    }

    public function testMySqliInitCommand()
    {
        $params = $this->_util->getParams();
        $params['driver_options'] = array(
            'mysqli_init_command' => 'SET AUTOCOMMIT=0;'
        );

        $db = Db\Db::factory($this->getDriver(), $params);

        $sql = 'SELECT @@AUTOCOMMIT as autocommit';

        $row = $db->fetchRow($sql);

        $this->assertEquals(0, $row['autocommit']);
    }

    public function getDriver()
    {
        return 'Mysqli';
    }

}
