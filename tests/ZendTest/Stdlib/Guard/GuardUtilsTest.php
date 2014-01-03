<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Guard;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stdlib\Guard\GuardUtils;
use Zend\Stdlib\ArrayObject;

/**
 * @group    Zend_StdLib_Guard
 * @covers   Zend\Stdlib\Guard\GuardUtils
 */
class GuardUtilsTest extends TestCase
{

    public function testGuardForArrayOrTraversableThrowsException()
    {
        $this->setExpectedException(
            'Zend\Stdlib\Exception\InvalidArgumentException',
            'Argument must be an array or Traversable, [string] given'
        );
        GuardUtils::guardForArrayOrTraversable('');
    }

    public function testGuardForArrayOrTraversableAllowsArray()
    {
        $this->assertNull(GuardUtils::guardForArrayOrTraversable(array()));
    }

    public function testGuardForArrayOrTraversableAllowsTraversable()
    {
        $traversable = new ArrayObject;
        $this->assertNull(GuardUtils::guardForArrayOrTraversable($traversable));
    }

    public function testGuardAgainstEmptyThrowsException()
    {
        $this->setExpectedException(
            'Zend\Stdlib\Exception\InvalidArgumentException',
            'Argument cannot be empty'
        );
        GuardUtils::guardAgainstEmpty('');
    }

    public function testGuardAgainstEmptyAllowsNonEmptyString()
    {
        $this->assertNull(GuardUtils::guardAgainstEmpty('foo'));
    }

    public function testGuardAgainstNullThrowsException()
    {
        $this->setExpectedException(
            'Zend\Stdlib\Exception\InvalidArgumentException',
            'Argument cannot be null'
        );
        GuardUtils::guardAgainstNull(null);
    }

    public function testGuardAgainstNullAllowsNonNull()
    {
        $this->assertNull(GuardUtils::guardAgainstNull('foo'));
    }

}
