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
class PdoPgsqlTest extends AbstractPdoTest
{

    protected $_numericDataTypes = array(
        Db\Db::INT_TYPE    => Db\Db::INT_TYPE,
        Db\Db::BIGINT_TYPE => Db\Db::BIGINT_TYPE,
        Db\Db::FLOAT_TYPE  => Db\Db::FLOAT_TYPE,
        'INTEGER'            => Db\Db::INT_TYPE,
        'SERIAL'             => Db\Db::INT_TYPE,
        'SMALLINT'           => Db\Db::INT_TYPE,
        'BIGINT'             => Db\Db::BIGINT_TYPE,
        'BIGSERIAL'          => Db\Db::BIGINT_TYPE,
        'DECIMAL'            => Db\Db::FLOAT_TYPE,
        'DOUBLE PRECISION'   => Db\Db::FLOAT_TYPE,
        'NUMERIC'            => Db\Db::FLOAT_TYPE,
        'REAL'               => Db\Db::FLOAT_TYPE
    );

    public function setup()
    {
        $this->markTestSkipped('This suite is skipped until Zend\Db can be refactored.');
    }
    
    public function testAdapterDescribeTablePrimaryAuto()
    {
        $desc = $this->_db->describeTable('zfbugs');

        $this->assertTrue($desc['bug_id']['PRIMARY']);
        $this->assertEquals(1, $desc['bug_id']['PRIMARY_POSITION']);
        $this->assertTrue($desc['bug_id']['IDENTITY']);
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
            'assigned_to'     => 'goofy'
        );
        $rowsAffected = $this->_db->insert('zfbugs', $row);
        $this->assertEquals(1, $rowsAffected);
        $lastInsertId = $this->_db->lastInsertId('zfbugs', 'bug_id');
        $lastSequenceId = $this->_db->lastSequenceId('zfbugs_bug_id_seq');
        $this->assertEquals((string) $lastInsertId, (string) $lastSequenceId,
            'Expected last insert id to be equal to last sequence id');
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
        $this->assertEquals((string) $lastInsertId, (string) $lastSequenceId,
            'Expected last insert id to be equal to last sequence id');
        $this->assertEquals('4', (string) $lastInsertId,
            'Expected new id to be 4');
    }

    public function testAdapterInsertDbExpr()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);
        $bug_description = $this->_db->quoteIdentifier('bug_description', true);

        $expr = new Db\Expr('2+3');

        $row = array (
            'bug_id'          => $expr,
            'bug_description' => 'New bug 5',
            'bug_status'      => 'NEW',
            'created_on'      => '2007-04-02',
            'updated_on'      => '2007-04-02',
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy',
            'verified_by'     => 'dduck'
        );
        $rowsAffected = $this->_db->insert('zfbugs', $row);
        $this->assertEquals(1, $rowsAffected);

        $value = $this->_db->fetchOne("SELECT $bug_description FROM $bugs WHERE $bug_id = 5");
        $this->assertEquals('New bug 5', $value);
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

    function getDriver()
    {
        return 'Pdo\Pgsql';
    }

    /**
     * @group ZF-3972
     */
    public function testAdapterCharacterVarying()
    {
        $this->_util->createTable('zf_pgsql_charvary',
                                  array('pg_id' => 'character varying(4) NOT NULL',
                                        'pg_info' => "character varying(1) NOT NULL DEFAULT 'A'::character varying"));
        $description = $this->_db->describeTable('zf_pgsql_charvary');
        $this->_util->dropTable('zf_pgsql_charvary');
        $this->assertEquals(null , $description['pg_id']['DEFAULT']);
        $this->assertEquals('A', $description['pg_info']['DEFAULT']);
    }

    /**
     * @group ZF-7640
     */
    public function testAdapterBpchar()
    {
        $this->_util->createTable('zf_pgsql_bpchar',
                                  array('pg_name' => "character(100) DEFAULT 'Default'::bpchar"));
        $description = $this->_db->describeTable('zf_pgsql_bpchar');
        $this->_util->dropTable('zf_pgsql_bpchar');
        $this->assertEquals('Default', $description['pg_name']['DEFAULT']);
    }

    /**
     * @group ZF-10160
     */
    public function testQuoteIdentifiersInSequence()
    {
        $this->_util->createSequence('camelCase_id_seq');
        $this->_db->nextSequenceId('camelCase_id_seq');
        $this->_db->nextSequenceId($this->_db->quoteIdentifier('camelCase_id_seq', true));
        $this->_db->lastSequenceId('camelCase_id_seq');
        $this->_db->lastSequenceId($this->_db->quoteIdentifier('camelCase_id_seq', true));

        require_once 'Zend/Db/Expr.php';
        $this->_db->lastSequenceId(new Zend_Db_Expr('camelCase_id_seq'));
        $lastId = $this->_db->lastSequenceId(new Zend_Db_Expr('camelCase_id_seq'));
        $this->assertEquals(2, $lastId);
        $this->_util->dropSequence('camelCase_id_seq');
    }
}
