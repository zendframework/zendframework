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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Test;
use Zend\Test;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class DbAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend\Test\DbAdapter
     */
    private $_adapter = null;

    public function setUp()
    {
        $this->_adapter = new Test\DbAdapter();
    }

    public function testAppendStatementToStack()
    {
        $stmt1 = Test\DbStatement::createSelectStatement( array() );
        $this->_adapter->appendStatementToStack($stmt1);

        $stmt2 = Test\DbStatement::createSelectStatement( array() );
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

    public function testQueryProfiler_EnabledByDefault()
    {
        $this->assertTrue($this->_adapter->getProfiler()->getEnabled());
    }

    public function testQueryPRofiler_PrepareStartsQueryProfiler()
    {
        $stmt = $this->_adapter->prepare("SELECT foo");

        $this->assertEquals(1, $this->_adapter->getProfiler()->getTotalNumQueries());

        $qp = $this->_adapter->getProfiler()->getLastQueryProfile();

        /* @var $qp Zend\Db\Profiler\Query */
        $this->assertFalse($qp->hasEnded());
    }

    public function testQueryProfiler_QueryStartEndsQueryProfiler()
    {
        $stmt = $this->_adapter->query("SELECT foo");

        $this->assertEquals(1, $this->_adapter->getProfiler()->getTotalNumQueries());

        $qp = $this->_adapter->getProfiler()->getLastQueryProfile();

        /* @var $qp Zend\Db\Profiler\Query */
        $this->assertTrue($qp->hasEnded());
    }

    public function testQueryProfiler_QueryBindWithParams()
    {
        $stmt = $this->_adapter->query("SELECT * FROM foo WHERE bar = ?", array(1234));

        $qp = $this->_adapter->getProfiler()->getLastQueryProfile();

        /* @var $qp Zend\Db\Profiler\Query */
        $this->assertEquals(array(1 => 1234), $qp->getQueryParams());
        $this->assertEquals("SELECT * FROM foo WHERE bar = ?", $qp->getQuery());
    }

    public function testQueryProfiler_PrepareBindExecute()
    {
        $var = 1234;

        $stmt = $this->_adapter->prepare("SELECT * FROM foo WHERE bar = ?");
        $stmt->bindParam(1, $var);

        $qp = $this->_adapter->getProfiler()->getLastQueryProfile();

        /* @var $qp Zend\Db\Profiler\Query */
        $this->assertEquals(array(1 => 1234), $qp->getQueryParams());
        $this->assertEquals("SELECT * FROM foo WHERE bar = ?", $qp->getQuery());
    }

    public function testGetSetQuoteIdentifierSymbol()
    {
        $this->assertEquals('', $this->_adapter->getQuoteIdentifierSymbol());
        $this->_adapter->setQuoteIdentifierSymbol('`');
        $this->assertEquals('`', $this->_adapter->getQuoteIdentifierSymbol());
    }
}
