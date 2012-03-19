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
    Zend\Db\Sql\Predicate\Expression;

class ExpressionTest extends TestCase
{

    public function testEmptyConstructorYieldsEmptyLiteralAndParameter()
    {
        $expression = new Expression();
        $this->assertEquals('', $expression->getExpression());
        $this->assertEmpty($expression->getParameters());
    }

    public function testCanPassLiteralAndParameterToConstructor()
    {
        $expression = new Expression();
        $predicate = new Expression('foo.bar = ?', 'bar');
        $this->assertEquals('foo.bar = ?', $predicate->getExpression());
        $this->assertEquals(array('bar'), $predicate->getParameters());
    }

    public function testLiteralIsMutable()
    {
        $expression = new Expression();
        $expression->setExpression('foo.bar = ?');
        $this->assertEquals('foo.bar = ?', $expression->getExpression());
    }

    public function testParameterIsMutable()
    {
        $expression = new Expression();
        $expression->setParameters(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $expression->getParameters());
    }

    public function testRetrievingWherePartsReturnsSpecificationArrayOfLiteralAndParametersAndArrayOfTypes()
    {
        $expression = new Expression();
        $expression->setExpression('foo.bar = ? AND id != ?')
                        ->setParameters(array('foo', 'bar'));
        $expected = array(array(
            'foo.bar = %s AND id != %s',
            array('foo', 'bar'),
            array(Expression::TYPE_VALUE, Expression::TYPE_VALUE),
        ));
        $test = $expression->getExpressionData();
        $this->assertEquals($expected, $test, var_export($test, 1));
    }

    public function testAllowZeroParameterValue()
    {
        $predicate = new Expression('foo.bar > ?', 0);
        $this->assertEquals(array(0), $predicate->getParameters());
    }
}
