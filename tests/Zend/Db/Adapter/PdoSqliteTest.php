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
class PdoSqliteTest extends AbstractPdoTest
{

    protected $_numericDataTypes = array(
        Db\Db::INT_TYPE    => Db\Db::INT_TYPE,
        Db\Db::BIGINT_TYPE => Db\Db::BIGINT_TYPE,
        Db\Db::FLOAT_TYPE  => Db\Db::FLOAT_TYPE,
        'INTEGER'            => Db\Db::BIGINT_TYPE,
        'REAL'               => Db\Db::FLOAT_TYPE
    );

    public function setup()
    {
        $this->markTestSkipped('This suite is skipped until Zend\Db can be refactored.');
    }
    
    /**
     * Test AUTO_QUOTE_IDENTIFIERS option
     * Case: Zend_Db::AUTO_QUOTE_IDENTIFIERS = true
     *
     * SQLite actually allows delimited identifiers to remain
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

        $this->assertEquals(1, $result1[0]['product_id']);

        $select = $this->_db->select();
        $select->from('ZFPRODUCTS');
        try {
            $stmt = $this->_db->query($select);
            $result2 = $stmt->fetchAll();
        } catch (\Zend\Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
            $this->fail('Unexpected exception '.get_class($e).' received: '.$e->getMessage());
        }

        $this->assertEquals($result1, $result2);
    }


    public function testAdapterConstructInvalidParamDbnameException()
    {
        $this->markTestSkipped($this->getDriver() . ' does not throw exception on missing dbname');
    }

    public function testAdapterConstructInvalidParamUsernameException()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support login credentials');
    }

    public function testAdapterConstructInvalidParamPasswordException()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support login credentials');
    }

    public function testAdapterInsertSequence()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support sequences');
    }

    /**
     * Used by:
     * - testAdapterOptionCaseFoldingNatural()
     * - testAdapterOptionCaseFoldingUpper()
     * - testAdapterOptionCaseFoldingLower()
     */
    protected function _testAdapterOptionCaseFoldingSetup(\Zend\Db\Adapter\AbstractAdapter $db)
    {
        $db->getConnection();
        $this->_util->setUp($db);
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

    public function testAdapterTransactionAutoCommit()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support transactions or concurrency');
    }

    public function testAdapterTransactionCommit()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support transactions or concurrency');
    }

    public function testAdapterTransactionRollback()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support transactions or concurrency');
    }

    /**
     * @return void
     * @see    http://framework.zend.com/issues/browse/ZF-2293
     */
    public function testAdapterSupportsLengthInTableMetadataForVarcharFields()
    {
        $metadata = $this->_db->describeTable('zfbugs');
        $this->assertEquals(100, $metadata['bug_description']['LENGTH']);
        $this->assertEquals(20, $metadata['bug_status']['LENGTH']);
    }

    public function getDriver()
    {
        return 'Pdo\Sqlite';
    }

}
