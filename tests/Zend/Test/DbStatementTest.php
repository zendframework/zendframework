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
class DbStatementTest extends \PHPUnit_Framework_TestCase
{
    public function testRowCountDefault()
    {
        $stmt = new Test\DbStatement();
        $this->assertEquals(0, $stmt->rowCount());
    }

    public function testSetRowCount()
    {
        $stmt = new Test\DbStatement();
        $stmt->setRowCount(10);
        $this->assertEquals(10, $stmt->rowCount());
    }

    public function testCreateSelectStatementWithRows()
    {
        $rows = array("foo", "bar");

        $stmt = Test\DbStatement::createSelectStatement($rows);

        $this->assertType('Zend\Test\DbStatement', $stmt);
        $this->assertEquals($rows, $stmt->fetchAll());
    }

    public function testCreateInsertStatementWithRowCount()
    {
        $stmt = Test\DbStatement::createInsertStatement(1234);

        $this->assertType('Zend\Test\DbStatement', $stmt);
        $this->assertEquals(1234, $stmt->rowCount());
    }

    public function testCreateUpdateStatementWithRowCount()
    {
        $stmt = Test\DbStatement::createUpdateStatement(1234);

        $this->assertType('Zend\Test\DbStatement', $stmt);
        $this->assertEquals(1234, $stmt->rowCount());
    }

    public function testCreateDeleteStatementWithRowCount()
    {
        $stmt = Test\DbStatement::createDeleteStatement(1234);

        $this->assertType('Zend\Test\DbStatement', $stmt);
        $this->assertEquals(1234, $stmt->rowCount());
    }

    public function testSetFetchRow()
    {
        $row = array("foo");

        $stmt = new Test\DbStatement();
        $stmt->append($row);

        $this->assertEquals($row, $stmt->fetch());
    }

    public function testFetchDefault()
    {
        $stmt = new Test\DbStatement();
        $this->assertFalse($stmt->fetch());
    }

    public function testFetchResult_FromEmptyResultStack()
    {
        $row = array("foo");

        $stmt = new Test\DbStatement();
        $stmt->append($row);
        $stmt->append($row);

        $this->assertTrue($stmt->fetch() !== false);
        $this->assertTrue($stmt->fetch() !== false);
        $this->assertFalse($stmt->fetch());
    }

    public function testFetchColumnDefault()
    {
        $stmt = new Test\DbStatement();
        $this->assertFalse($stmt->fetchColumn());
    }

    public function testFetchColumn()
    {
        $row = array("foo" => "bar", "bar" => "baz");

        $stmt = new Test\DbStatement();
        $stmt->append($row);

        $this->assertEquals("baz", $stmt->fetchColumn(1));
    }

    public function testFetchColumn_OutOfBounds()
    {
        $this->setExpectedException("Zend\Db\Statement\Exception");

        $row = array("foo" => "bar", "bar" => "baz");

        $stmt = new Test\DbStatement();
        $stmt->append($row);

        $stmt->fetchColumn(1234);
    }

    public function testFetchObject()
    {
        $row = array("foo" => "bar", "bar" => "baz");

        $stmt = new Test\DbStatement();
        $stmt->append($row);

        $object = $stmt->fetchObject();
        $this->assertInternalType('object', $object);
        $this->assertEquals('bar', $object->foo);
        $this->assertEquals('baz', $object->bar);
    }

    public function testFetchObject_ClassNotExists_ThrowsException()
    {
        $this->setExpectedException("Zend\Db\Statement\Exception");

        $row = array("foo" => "bar", "bar" => "baz");

        $stmt = new Test\DbStatement();
        $stmt->append($row);

        $object = $stmt->fetchObject("anInvalidClassName");
    }
}
