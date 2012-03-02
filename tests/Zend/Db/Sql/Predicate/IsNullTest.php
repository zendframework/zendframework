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
    Zend\Db\Sql\Predicate\IsNotNull;

class IsNotNullTest extends TestCase
{
    public function setUp()
    {
        $this->predicate = new IsNotNull();
    }

    public function testEmptyConstructorYieldsNullIdentifier()
    {
        $this->assertNull($this->predicate->getIdentifier());
    }

    public function testSpecificationHasSaneDefaultValue()
    {
        $this->assertEquals('%1$s IS NOT NULL', $this->predicate->getSpecification());
    }

    public function testCanPassIdentifierToConstructor()
    {
        $isnull = new IsNotNull('foo.bar');
        $this->assertEquals('foo.bar', $isnull->getIdentifier());
    }

    public function testIdentifierIsMutable()
    {
        $this->predicate->setIdentifier('foo.bar');
        $this->assertEquals('foo.bar', $this->predicate->getIdentifier());
    }

    public function testSpecificationIsMutable()
    {
        $this->predicate->setSpecification('%1$s NOT NULL');
        $this->assertEquals('%1$s NOT NULL', $this->predicate->getSpecification());
    }

    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndArrayOfTypes()
    {
        $this->predicate->setIdentifier('foo.bar');
        $expected = array(array(
            $this->predicate->getSpecification(),
            array('foo.bar'),
            array(IsNotNull::TYPE_IDENTIFIER),
        ));
        $this->assertEquals($expected, $this->predicate->getWhereParts());
    }
}
