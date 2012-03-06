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
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Db\Sql\Predicate;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Db\Sql\Predicate\Operator;

class OperatorTest extends TestCase
{
    public function setUp()
    {
        $this->predicate = new Operator();
    }

    public function testEmptyConstructorYieldsNullLeftAndRightValues()
    {
        $this->assertNull($this->predicate->getLeft());
        $this->assertNull($this->predicate->getRight());
    }

    public function testEmptyConstructorYieldsDefaultsForOperatorAndLeftAndRightTypes()
    {
        $this->assertEquals(Operator::OP_EQ, $this->predicate->getOperator());
        $this->assertEquals(Operator::TYPE_IDENTIFIER, $this->predicate->getLeftType());
        $this->assertEquals(Operator::TYPE_VALUE, $this->predicate->getRightType());
    }

    public function testCanPassAllValuesToConstructor()
    {
        $predicate = new Operator('bar', '>=', 'foo.bar', Operator::TYPE_VALUE, Operator::TYPE_IDENTIFIER);
        $this->assertEquals(Operator::OP_GTE, $predicate->getOperator());
        $this->assertEquals('bar', $predicate->getLeft());
        $this->assertEquals('foo.bar', $predicate->getRight());
        $this->assertEquals(Operator::TYPE_VALUE, $predicate->getLeftType());
        $this->assertEquals(Operator::TYPE_IDENTIFIER, $predicate->getRightType());
    }

    public function testLeftIsMutable()
    {
        $this->predicate->setLeft('foo.bar');
        $this->assertEquals('foo.bar', $this->predicate->getLeft());
    }

    public function testRightIsMutable()
    {
        $this->predicate->setRight('bar');
        $this->assertEquals('bar', $this->predicate->getRight());
    }

    public function testLeftTypeIsMutable()
    {
        $this->predicate->setLeftType(Operator::TYPE_VALUE);
        $this->assertEquals(Operator::TYPE_VALUE, $this->predicate->getLeftType());
    }

    public function testRightTypeIsMutable()
    {
        $this->predicate->setRightType(Operator::TYPE_IDENTIFIER);
        $this->assertEquals(Operator::TYPE_IDENTIFIER, $this->predicate->getRightType());
    }

    public function testOperatorIsMutable()
    {
        $this->predicate->setOperator(Operator::OP_LTE);
        $this->assertEquals(Operator::OP_LTE, $this->predicate->getOperator());
    }

    public function testRetrievingWherePartsReturnsSpecificationArrayOfLeftAndRightAndArrayOfTypes()
    {
        $this->predicate->setLeft('foo')
                        ->setOperator('>=')
                        ->setRight('foo.bar')
                        ->setLeftType(Operator::TYPE_VALUE)
                        ->setRightType(Operator::TYPE_IDENTIFIER);
        $expected = array(array(
            '%s >= %s',
            array('foo', 'foo.bar'),
            array(Operator::TYPE_VALUE, Operator::TYPE_IDENTIFIER),
        ));
        $test = $this->predicate->getWhereParts();
        $this->assertEquals($expected, $test, var_export($test, 1));
    }
}
