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
    Zend\Db\Sql\Predicate\Literal;

class LiteralTest extends TestCase
{
    public function setUp()
    {
        $this->predicate = new Literal();
    }

    public function testEmptyConstructorYieldsEmptyLiteralAndParameter()
    {
        $this->assertEquals('', $this->predicate->getLiteral());
        $this->assertNull($this->predicate->getParameter());
    }

    public function testCanPassLiteralAndParameterToConstructor()
    {
        $predicate = new Literal('foo.bar = ?', 'bar');
        $this->assertEquals('foo.bar = ?', $predicate->getLiteral());
        $this->assertEquals(array('bar'), $predicate->getParameter());
    }

    public function testLiteralIsMutable()
    {
        $this->predicate->setLiteral('foo.bar = ?');
        $this->assertEquals('foo.bar = ?', $this->predicate->getLiteral());
    }

    public function testParameterIsMutable()
    {
        $this->predicate->setParameter(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $this->predicate->getParameter());
    }

    public function testRetrievingWherePartsReturnsSpecificationArrayOfLiteralAndParametersAndArrayOfTypes()
    {
        $this->predicate->setLiteral('foo.bar = ? AND id != ?')
                        ->setParameter(array('foo', 'bar'));
        $expected = array(array(
            'foo.bar = %s AND id != %s',
            array('foo', 'bar'),
            array(Literal::TYPE_VALUE, Literal::TYPE_VALUE),
        ));
        $test = $this->predicate->getWhereParts();
        $this->assertEquals($expected, $test, var_export($test, 1));
    }
}
