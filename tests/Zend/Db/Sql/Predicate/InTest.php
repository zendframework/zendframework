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
    Zend\Db\Sql\Predicate\In;

class InTest extends TestCase
{
    /**
     * @var In
     */
    protected $in = null;

    public function setUp()
    {
        $this->in = new In();
    }

    public function testEmptyConstructorYieldsNullIdentifierAndValueSet()
    {
        $this->assertNull($this->in->getIdentifier());
        $this->assertNull($this->in->getValueSet());
    }

    public function testSpecificationHasSaneDefaultValue()
    {
        $this->assertEquals('%1$s IN (%2$s)', $this->in->getSpecification());
    }

    public function testCanPassIdentifierAndValueSetToConstructor()
    {
        $predicate = new In('foo.bar', array(1, 2));
        $this->assertEquals('foo.bar', $predicate->getIdentifier());
        $this->assertEquals(array(1, 2), $predicate->getValueSet());
    }

    public function testIdentifierIsMutable()
    {
        $this->in->setIdentifier('foo.bar');
        $this->assertEquals('foo.bar', $this->in->getIdentifier());
    }

    public function testValueSetIsMutable()
    {
        $this->in->setValueSet(array(1, 2));
        $this->assertEquals(array(1, 2), $this->in->getValueSet());
    }

    public function testSpecificationIsMutable()
    {
        $this->in->setSpecification('%1$s IS IN (%2$s)');
        $this->assertEquals('%1$s IS IN (%2$s)', $this->in->getSpecification());
    }

    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndValuesAndArrayOfTypes()
    {
        $this->in->setIdentifier('foo.bar')
                        ->setValueSet(array(1, 2, 3));
        $expected = array(array(
            $this->in->getSpecification(),
            array('foo.bar', 1, 2, 3),
            array(In::TYPE_IDENTIFIER, In::TYPE_VALUE, In::TYPE_VALUE, In::TYPE_VALUE),
        ));
        $this->assertEquals($expected, $this->in->getExpressionData());
    }
}
