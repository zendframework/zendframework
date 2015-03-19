<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql\Ddl\Column;

class AbstractPrecisionColumnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Zend\Db\Sql\Ddl\Column\AbstractPrecisionColumn::setDigits
     */
    public function testSetDigits()
    {
        $column = $this->getMockForAbstractClass('Zend\Db\Sql\Ddl\Column\AbstractPrecisionColumn', array(
            'foo', 10
        ));
        $this->assertEquals(10, $column->getDigits());
        $this->assertSame($column, $column->setDigits(12));
        $this->assertEquals(12, $column->getDigits());
    }

    /**
     * @covers Zend\Db\Sql\Ddl\Column\AbstractPrecisionColumn::getDigits
     */
    public function testGetDigits()
    {
        $column = $this->getMockForAbstractClass('Zend\Db\Sql\Ddl\Column\AbstractPrecisionColumn', array(
            'foo', 10
        ));
        $this->assertEquals(10, $column->getDigits());
    }

    /**
     * @covers Zend\Db\Sql\Ddl\Column\AbstractPrecisionColumn::setDecimal
     */
    public function testSetDecimal()
    {
        $column = $this->getMockForAbstractClass('Zend\Db\Sql\Ddl\Column\AbstractPrecisionColumn', array(
            'foo', 10, 5
        ));
        $this->assertEquals(5, $column->getDecimal());
        $this->assertSame($column, $column->setDecimal(2));
        $this->assertEquals(2, $column->getDecimal());
    }

    /**
     * @covers Zend\Db\Sql\Ddl\Column\AbstractPrecisionColumn::getDecimal
     */
    public function testGetDecimal()
    {
        $column = $this->getMockForAbstractClass('Zend\Db\Sql\Ddl\Column\AbstractPrecisionColumn', array(
            'foo', 10, 5
        ));
        $this->assertEquals(5, $column->getDecimal());
    }

    /**
     * @covers Zend\Db\Sql\Ddl\Column\AbstractPrecisionColumn::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = $this->getMockForAbstractClass('Zend\Db\Sql\Ddl\Column\AbstractPrecisionColumn', array(
            'foo', 10, 5
        ));

        $this->assertEquals(
            array(array('%s %s NOT NULL', array('foo', 'INTEGER(10,5)'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL))),
            $column->getExpressionData()
        );
    }
}
