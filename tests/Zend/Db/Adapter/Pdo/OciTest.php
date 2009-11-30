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

require_once 'Zend/Db/Adapter/Pdo/TestCommon.php';

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
class Zend_Db_Adapter_Pdo_OciTest extends Zend_Db_Adapter_Pdo_TestCommon
{

    protected $_numericDataTypes = array(
        Zend_Db::INT_TYPE    => Zend_Db::INT_TYPE,
        Zend_Db::BIGINT_TYPE => Zend_Db::BIGINT_TYPE,
        Zend_Db::FLOAT_TYPE  => Zend_Db::FLOAT_TYPE,
        'BINARY_DOUBLE'      => Zend_Db::FLOAT_TYPE,
        'BINARY_FLOAT'       => Zend_Db::FLOAT_TYPE,
        'NUMBER'             => Zend_Db::FLOAT_TYPE
    );

    public function testAdapterDescribeTablePrimaryAuto()
    {
        $this->markTestSkipped('Oracle does not support auto-increment');
    }

    public function testAdapterDescribeTablePrimaryKeyColumn()
    {
        $desc = $this->_db->describeTable('zfproducts');

        $this->assertEquals('zfproducts',        $desc['product_id']['TABLE_NAME']);
        $this->assertEquals('product_id',        $desc['product_id']['COLUMN_NAME']);
        $this->assertEquals(1,                   $desc['product_id']['COLUMN_POSITION']);
        $this->assertEquals('',                  $desc['product_id']['DEFAULT']);
        $this->assertFalse(                      $desc['product_id']['NULLABLE']);
        $this->assertEquals(0,                   $desc['product_id']['SCALE']);
        // Oracle reports precsion 11 for integers
        $this->assertEquals(11,                  $desc['product_id']['PRECISION']);
        $this->assertTrue(                       $desc['product_id']['PRIMARY'], 'Expected product_id to be a primary key');
        $this->assertEquals(1,                   $desc['product_id']['PRIMARY_POSITION']);
        $this->assertFalse(                      $desc['product_id']['IDENTITY']);
    }

    public function testAdapterInsert()
    {
        $row = array (
            'product_id'   => new Zend_Db_Expr($this->_db->quoteIdentifier('zfproducts_seq').'.NEXTVAL'),
            'product_name' => 'Solaris',
        );
        $rowsAffected = $this->_db->insert('zfproducts', $row);
        $this->assertEquals(1, $rowsAffected);
        $lastInsertId = $this->_db->lastInsertId('zfproducts', null); // implies 'products_seq'
        $lastSequenceId = $this->_db->lastSequenceId('zfproducts_seq');
        $this->assertType('string', $lastInsertId);
        $this->assertType('string', $lastSequenceId);
        $this->assertEquals('4', (string) $lastInsertId, 'Expected new id to be 4');
        $this->assertEquals('4', (string) $lastSequenceId, 'Expected new id to be 4');
    }

    public function testAdapterInsertDbExpr()
    {
        $row = array (
            'product_id'   => new Zend_Db_Expr($this->_db->quoteIdentifier('zfproducts_seq').'.NEXTVAL'),
            'product_name' => new Zend_Db_Expr('UPPER(\'Solaris\')')
        );
        $rowsAffected = $this->_db->insert('zfproducts', $row);
        $this->assertEquals(1, $rowsAffected);
        $product_id = $this->_db->quoteIdentifier('product_id', true);
        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id = 4");
        $result = $this->_db->fetchAll($select);
        $this->assertType('array', $result);
        $this->assertEquals('SOLARIS', $result[0]['product_name']);
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
        $value = $this->_db->quote($string);
        $this->assertEquals("'St John\"s Wort'", $value);
    }

    /**
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
     * test that quoteTableAs() accepts a string and an alias,
     * and returns each as delimited identifiers.
     * Oracle does not want the 'AS' in between.
     */
    public function testAdapterQuoteTableAs()
    {
        $string = "foo";
        $alias = "bar";
        $value = $this->_db->quoteTableAs($string, $alias);
        $this->assertEquals('"foo" "bar"', $value);
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
        $this->assertEquals($expected, stream_get_contents($value[0]['doc_clob']));
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
        $this->assertEquals($expected, stream_get_contents($value['doc_clob']));
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
        $this->assertEquals($expected, stream_get_contents($value[1]['doc_clob']));
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
        $this->assertEquals($expected, stream_get_contents($value[0]));
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
        $this->assertEquals($expected, stream_get_contents($value));
    }

    /**
     * @group ZF-8399
     */
    public function testLongQueryWithTextField()
    {
        $this->markTestSkipped($this->getDriver() . ' does not have TEXT field type');
    }

    public function getDriver()
    {
        return 'Pdo_Oci';
    }
}
