<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql;

use Zend\Db\Sql\TableIdentifier;

/**
 * Tests for {@see \Zend\Db\Sql\TableIdentifier}
 *
 * @covers \Zend\Db\Sql\TableIdentifier
 */
class TableIdentifierTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTable()
    {
        $tableIdentifier = new TableIdentifier('foo');

        $this->assertSame('foo', $tableIdentifier->getTable());
    }

    public function testGetDefaultSchema()
    {
        $tableIdentifier = new TableIdentifier('foo');

        $this->assertNull($tableIdentifier->getSchema());
    }

    public function testGetSchema()
    {
        $tableIdentifier = new TableIdentifier('foo', 'bar');

        $this->assertSame('bar', $tableIdentifier->getSchema());
    }

    public function testGetTableFromObjectStringCast()
    {
        $table = $this->getMock('stdClass', '__invoke');

        $table->expects($this->once())->method('__invoke')->will($this->returnValue('castResult'));

        $tableIdentifier = new TableIdentifier($table);

        $this->assertSame('castResult', $tableIdentifier->getTable());
        $this->assertSame('castResult', $tableIdentifier->getTable());
    }
}
