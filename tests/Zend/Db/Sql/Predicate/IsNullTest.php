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

    public function testEmptyConstructorYieldsNullIdentifier()
    {
        $isNotNull = new IsNotNull();
        $this->assertNull($isNotNull->getIdentifier());
    }

    public function testSpecificationHasSaneDefaultValue()
    {
        $isNotNull = new IsNotNull();
        $this->assertEquals('%1$s IS NOT NULL', $isNotNull->getSpecification());
    }

    public function testCanPassIdentifierToConstructor()
    {
        $isNotNull = new IsNotNull();
        $isnull = new IsNotNull('foo.bar');
        $this->assertEquals('foo.bar', $isnull->getIdentifier());
    }

    public function testIdentifierIsMutable()
    {
        $isNotNull = new IsNotNull();
        $isNotNull->setIdentifier('foo.bar');
        $this->assertEquals('foo.bar', $isNotNull->getIdentifier());
    }

    public function testSpecificationIsMutable()
    {
        $isNotNull = new IsNotNull();
        $isNotNull->setSpecification('%1$s NOT NULL');
        $this->assertEquals('%1$s NOT NULL', $isNotNull->getSpecification());
    }

    public function testRetrievingWherePartsReturnsSpecificationArrayOfIdentifierAndArrayOfTypes()
    {
        $isNotNull = new IsNotNull();
        $isNotNull->setIdentifier('foo.bar');
        $expected = array(array(
            $isNotNull->getSpecification(),
            array('foo.bar'),
            array(IsNotNull::TYPE_IDENTIFIER),
        ));
        $this->assertEquals($expected, $isNotNull->getExpressionData());
    }
}
