<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql\Ddl\Column;

use Zend\Db\Sql\Ddl\Column\Binary;

class BinaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Zend\Db\Sql\Ddl\Column\Binary::setLength
     */
    public function testSetLength()
    {
        $column = new Binary('foo', 55);
        $this->assertEquals(55, $column->getLength());
        $this->assertSame($column, $column->setLength(20));
        $this->assertEquals(20, $column->getLength());
    }

    /**
     * @covers Zend\Db\Sql\Ddl\Column\Binary::getLength
     */
    public function testGetLength()
    {
        $column = new Binary('foo', 55);
        $this->assertEquals(55, $column->getLength());
    }

    /**
     * @covers Zend\Db\Sql\Ddl\Column\Binary::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Binary('foo', 10000000);
        $this->assertEquals(
            array(array('%s %s NOT NULL', array('foo', 'BINARY(10000000)'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL))),
            $column->getExpressionData()
        );
    }

}
