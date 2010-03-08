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
 * @see Zend_Db_Adapter_TestCommon
 */

/**
 * @see Zend_Db_Adapter_Oracle
 */

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Adapter
 */
class Zend_Db_Adapter_OracleTest extends Zend_Db_Adapter_TestCommon
{

    protected $_numericDataTypes = array(
        Zend_Db::INT_TYPE    => Zend_Db::INT_TYPE,
        Zend_Db::BIGINT_TYPE => Zend_Db::BIGINT_TYPE,
        Zend_Db::FLOAT_TYPE  => Zend_Db::FLOAT_TYPE,
        'BINARY_DOUBLE'      => Zend_Db::FLOAT_TYPE,
        'BINARY_FLOAT'       => Zend_Db::FLOAT_TYPE,
        'NUMBER'             => Zend_Db::FLOAT_TYPE,
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

    /**
     * Test the Adapter's fetchAll() method.
     */
    public function testAdapterFetchAll()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $result = $this->_db->fetchAll("SELECT * FROM $products WHERE $product_id > :id ORDER BY $product_id ASC", array(":id"=>1));
        $this->assertEquals(2, count($result));
        $this->assertThat($result[0], $this->arrayHasKey('product_id'));
        $this->assertEquals('2', $result[0]['product_id']);
    }

    /**
     * ZF-4330: Oracle binds variables by name
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
        $result = $this->_db->fetchAll("SELECT * FROM $products WHERE $product_id > :id ORDER BY $product_id ASC", array(":id"=>1), Zend_Db::FETCH_ASSOC);
        $this->assertEquals(2, count($result));
        $this->assertType('array', $result[0]);
        $this->assertEquals(2, count($result[0])); // count columns
        $this->assertEquals(2, $result[0][$col_name]);

        // Test numeric and associative array
        // OCI8 driver does not support fetchAll(FETCH_BOTH), use fetch() in a loop instead

        // Ensure original fetch mode has been retained
        $result = $this->_db->fetchAll("SELECT * FROM $products WHERE $product_id > :id ORDER BY $product_id", array(":id"=>1));
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

        $result = $this->_db->fetchAssoc("SELECT * FROM $products WHERE $product_id > :id ORDER BY $product_id DESC", array(":id"=>1));
        foreach ($result as $idKey => $row) {
            $this->assertThat($row, $this->arrayHasKey('product_id'));
            $this->assertEquals($idKey, $row['product_id']);
        }
    }

    /**
     * ZF-4275: Oracle binds variables by name
     * Test that fetchAssoc() still fetched an associative array
     * after the adapter's default fetch mode is set to something else.
     */
    public function testAdapterFetchAssocAfterSetFetchMode()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $this->_db->fetchAssoc("SELECT * FROM $products WHERE $product_id > :id ORDER BY $product_id DESC", array(":id"=>1));
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

        $result = $this->_db->fetchCol("SELECT * FROM $products WHERE $product_id > :id ORDER BY $product_id ASC", array(":id"=>1));
        $this->assertEquals(2, count($result)); // count rows
        $this->assertEquals(2, $result[0]);
        $this->assertEquals(3, $result[1]);
    }

    /**
     * ZF-4275: Oracle binds variables by name
     * Test that fetchCol() still fetched an associative array
     * after the adapter's default fetch mode is set to something else.
     */
    public function testAdapterFetchColAfterSetFetchMode()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $this->_db->fetchCol("SELECT * FROM $products WHERE $product_id > :id ORDER BY $product_id ASC", array(":id"=>1));
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
        $result = $this->_db->fetchOne("SELECT $product_name FROM $products WHERE $product_id > :id ORDER BY $product_id", array(":id"=>1));
        $this->assertEquals($prod, $result);
    }


    /**
     * ZF-4275: Oracle binds variables by name
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
        $result = $this->_db->fetchOne("SELECT $product_name FROM $products WHERE $product_id > :id ORDER BY $product_id", array(":id"=>1));
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
        $result = $this->_db->fetchPairs("SELECT $product_id, $product_name FROM $products WHERE $product_id > :id ORDER BY $product_id ASC", array(":id"=>1));
        $this->assertEquals(2, count($result)); // count rows
        $this->assertEquals($prod, $result[2]);
    }

    /**
     * ZF-4275: Oracle binds variables by name
     * Test the Adapter's fetchPairs() method.
     */
    public function testAdapterFetchPairsAfterSetFetchMode()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
        $prod = 'Linux';
        $result = $this->_db->fetchPairs("SELECT $product_id, $product_name FROM $products WHERE $product_id > :id ORDER BY $product_id ASC", array(":id"=>1));
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

        $result = $this->_db->fetchRow("SELECT * FROM $products WHERE $product_id > :id ORDER BY $product_id", array(":id"=>1));
        $this->assertEquals(2, count($result)); // count columns
        $this->assertEquals(2, $result['product_id']);
    }

    /**
     * ZF-4330: Oracle binds variables by name
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
        $result = $this->_db->fetchRow("SELECT * FROM $products WHERE $product_id > :id ORDER BY $product_id", array(":id"=>1), Zend_Db::FETCH_ASSOC);
        $this->assertType('array', $result);
        $this->assertEquals(2, count($result)); // count columns
        $this->assertEquals(2, $result['product_id']);

        // Test numeric and associative array
        // OCI8 driver does not support fetchAll(FETCH_BOTH), use fetch() in a loop instead

        // Ensure original fetch mode has been retained
        $result = $this->_db->fetchRow("SELECT * FROM $products WHERE $product_id > :id ORDER BY $product_id", array(":id"=>1));
        $this->assertType('object', $result);
        $this->assertEquals(2, $result->$col_name);
    }

    public function testAdapterInsert()
    {
        $row = array (
            'product_id'   => new Zend_Db_Expr($this->_db->quoteIdentifier('zfproducts_seq').'.NEXTVAL'),
            'product_name' => 'Solaris',
        );
        $rowsAffected = $this->_db->insert('zfproducts', $row);
        $this->assertEquals(1, $rowsAffected);
        $lastInsertId = $this->_db->lastInsertId('zfproducts', null); // implies 'zfproducts_seq'
        $lastSequenceId = $this->_db->lastSequenceId('zfproducts_seq');
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
        $value = $this->_db->quote('St John"s Wort');
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
        $value = $this->_db->quoteInto('id=?', 'St John"s Wort');
        $this->assertEquals("id='St John\"s Wort'", $value);
    }

    /**
     * test that quoteInto() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteIntoSingleQuote()
    {
        $value = $this->_db->quoteInto('id = ?', 'St John\'s Wort');
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
    public function testAdapterLobAsString()
    {
        $this->assertFalse($this->_db->getLobAsString());
        $this->_db->setLobAsString(true);
        $this->assertTrue($this->_db->getLobAsString());
    }

    /**
     * @group ZF-5146
     */
    public function testAdapterLobAsStringFromDriverOptions()
    {
        $params = $this->_util->getParams();
        $params['driver_options'] = array(
            'lob_as_string' => true
        );
        $db = Zend_Db::factory($this->getDriver(), $params);
        $this->assertTrue($db->getLobAsString());
    }

    /**
     * @group ZF-5146
     */
    public function testAdapterReadClobFetchRow()
    {
        $documents = $this->_db->quoteIdentifier('zfdocuments');
        $document_id = $this->_db->quoteIdentifier('doc_id');
        $value = $this->_db->fetchRow("SELECT * FROM $documents WHERE $document_id = 1");
        $this->assertType('OCI-Lob', $value['doc_clob']);
        $expected = 'this is the clob that never ends...'.
                    'this is the clob that never ends...'.
                    'this is the clob that never ends...';
        $lob = $value['doc_clob'];
        $this->assertEquals($expected, $lob->read($lob->size()));
    }

    /**
     * @group ZF-5146
     */
    public function testAdapterReadClobFetchRowLobAsString()
    {
        $this->_db->setLobAsString(true);
        parent::testAdapterReadClobFetchRow();
    }

    /**
     * @group ZF-5146
     */
    public function testAdapterReadClobFetchAssoc()
    {
        $documents = $this->_db->quoteIdentifier('zfdocuments');
        $document_id = $this->_db->quoteIdentifier('doc_id');
        $value = $this->_db->fetchAssoc("SELECT * FROM $documents WHERE $document_id = 1");
        $this->assertType('OCI-Lob', $value[1]['doc_clob']);
        $expected = 'this is the clob that never ends...'.
                    'this is the clob that never ends...'.
                    'this is the clob that never ends...';
        $lob = $value[1]['doc_clob'];
        $this->assertEquals($expected, $lob->read($lob->size()));
    }

    /**
     * @group ZF-5146
     */
    public function testAdapterReadClobFetchAssocLobAsString()
    {
        $this->_db->setLobAsString(true);
        parent::testAdapterReadClobFetchAssoc();
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
        $this->assertType('OCI-Lob', $value);
        $expected = 'this is the clob that never ends...'.
                    'this is the clob that never ends...'.
                    'this is the clob that never ends...';
        $lob = $value;
        $this->assertEquals($expected, $lob->read($lob->size()));
    }

    /**
     * @group ZF-5146
     */
    public function testAdapterReadClobFetchOneLobAsString()
    {
        $this->_db->setLobAsString(true);
        parent::testAdapterReadClobFetchOne();
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

    public function testAdapterOptionCaseFoldingUpper()
    {
        $this->markTestIncomplete($this->getDriver() . ' does not support case-folding array keys yet.');
    }

    public function testAdapterOptionCaseFoldingLower()
    {
        $this->markTestIncomplete($this->getDriver() . ' does not support case-folding array keys yet.');
    }

    public function testAdapterTransactionCommit()
    {
        $this->markTestIncomplete($this->getDriver() . ' is having trouble with transactions');
    }

    public function testAdapterTransactionRollback()
    {
        $this->markTestIncomplete($this->getDriver() . ' is having trouble with transactions');
    }

    public function testAdapterAlternateStatement()
    {
        $this->_testAdapterAlternateStatement('Test_OracleStatement');
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
        return 'Oracle';
    }
}
