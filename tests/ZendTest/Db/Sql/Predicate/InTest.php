<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql\Predicate;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\In;

class InTest extends TestCase
{
    public function testEmptyConstructorYieldsNullIdentifierAndValueSet()
    {
        $in = new In();
        $this->assertNull($in->getIdentifier());
        $this->assertNull($in->getValueSet());
    }

    public function testCanPassIdentifierAndValueSetToConstructor()
    {
        $in = new In('foo.bar', array(1, 2));
        $this->assertEquals('foo.bar', $in->getIdentifier());
        $this->assertEquals(array(1, 2), $in->getValueSet());
    }

    public function testIdentifierIsMutable()
    {
        $in = new In();
        $in->setIdentifier('foo.bar');
        $this->assertEquals('foo.bar', $in->getIdentifier());
    }

    public function testValueSetIsMutable()
    {
        $in = new In();
        $in->setValueSet(array(1, 2));
        $this->assertEquals(array(1, 2), $in->getValueSet());
    }

    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndValuesAndArrayOfTypes()
    {
        $in = new In();
        $in->setIdentifier('foo.bar')
            ->setValueSet(array(1, 2, 3));
        $expected = array(array(
            '%s IN (%s, %s, %s)',
            array('foo.bar', 1, 2, 3),
            array(In::TYPE_IDENTIFIER, In::TYPE_VALUE, In::TYPE_VALUE, In::TYPE_VALUE),
        ));
        $this->assertEquals($expected, $in->getExpressionData());

        $in->setIdentifier('foo.bar')
            ->setValueSet(array(
                array(1=>In::TYPE_LITERAL),
                array(2=>In::TYPE_VALUE),
                array(3=>In::TYPE_LITERAL),
            ));
        $expected = array(array(
            '%s IN (%s, %s, %s)',
            array('foo.bar', 1, 2, 3),
            array(In::TYPE_IDENTIFIER, In::TYPE_LITERAL, In::TYPE_VALUE, In::TYPE_LITERAL),
        ));
        $qqq = $in->getExpressionData();
        $this->assertEquals($expected, $in->getExpressionData());
    }

    public function testGetExpressionDataWithSubselect()
    {
        $select = new Select;
        $in = new In('foo', $select);
        $expected = array(array(
            '%s IN %s',
            array('foo', $select),
            array($in::TYPE_IDENTIFIER, $in::TYPE_VALUE)
        ));
        $this->assertEquals($expected, $in->getExpressionData());
    }

    public function testGetExpressionDataWithSubselectAndIdentifier()
    {
        $select = new Select;
        $in = new In('foo', $select);
        $expected = array(array(
            '%s IN %s',
            array('foo', $select),
            array($in::TYPE_IDENTIFIER, $in::TYPE_VALUE)
        ));
        $this->assertEquals($expected, $in->getExpressionData());
    }

    public function testGetExpressionDataWithSubselectAndArrayIdentifier()
    {
        $select = new Select;
        $in = new In(array('foo', 'bar'), $select);
        $expected = array(array(
            '(%s, %s) IN %s',
            array('foo', 'bar', $select),
            array($in::TYPE_IDENTIFIER, $in::TYPE_IDENTIFIER, $in::TYPE_VALUE)
        ));
        $this->assertEquals($expected, $in->getExpressionData());
    }
}
