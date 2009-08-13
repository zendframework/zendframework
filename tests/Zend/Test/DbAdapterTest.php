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
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(__FILE__)."/../../TestHelper.php";

require_once "Zend/Test/DbAdapter.php";
require_once "Zend/Test/DbStatement.php";

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class Zend_Test_DbAdapterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Test_DbAdapter
     */
    private $_adapter = null;

    public function setUp()
    {
        $this->_adapter = new Zend_Test_DbAdapter();
    }

    public function testAppendStatementToStack()
    {
        $stmt1 = Zend_Test_DbStatement::createSelectStatement( array() );
        $this->_adapter->appendStatementToStack($stmt1);

        $stmt2 = Zend_Test_DbStatement::createSelectStatement( array() );
        $this->_adapter->appendStatementToStack($stmt2);

        $this->assertSame($stmt2, $this->_adapter->query("foo"));
        $this->assertSame($stmt1, $this->_adapter->query("foo"));
    }

    public function testAppendLastInsertId()
    {
        $this->_adapter->appendLastInsertIdToStack(1);
        $this->_adapter->appendLastInsertIdToStack(2);

        $this->assertEquals(2, $this->_adapter->lastInsertId());
        $this->assertEquals(1, $this->_adapter->lastInsertId());
    }

    public function testLastInsertIdDefault()
    {
        $this->assertFalse($this->_adapter->lastInsertId());
    }

    public function testListTablesDefault()
    {
        $this->assertEquals(array(), $this->_adapter->listTables());
    }

    public function testSetListTables()
    {
        $this->_adapter->setListTables(array("foo", "bar"));
        $this->assertEquals(array("foo", "bar"), $this->_adapter->listTables());
    }

    public function testDescribeTableDefault()
    {
        $this->assertEquals(array(), $this->_adapter->describeTable("foo"));
    }

    public function testDescribeTable()
    {
        $this->_adapter->setDescribeTable("foo", array("bar"));
        $this->assertEquals(array("bar"), $this->_adapter->describeTable("foo"));
    }

    public function testConnect()
    {
        $this->assertFalse($this->_adapter->isConnected());
        $this->_adapter->query("foo");
        $this->assertTrue($this->_adapter->isConnected());
        $this->_adapter->closeConnection();
        $this->assertFalse($this->_adapter->isConnected());
    }

    public function testAppendLimitToSql()
    {
        $sql = $this->_adapter->limit("foo", 10, 20);
        $this->assertEquals(
            "foo LIMIT 20,10", $sql
        );
    }
}
