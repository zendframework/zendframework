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
use ZendTest\Stdlib\TestAsset\GuardedObject;
use Zend\Stdlib\ArrayObject;

/**
 * @requires PHP 5.4
 * @group    Zend_StdLib_Guard
 * @covers   Zend\Stdlib\Guard\ArrayOrTraversableGuardTrait
 */
class ArrayOrTraversableGuardTraitTest extends TestCase
{

    public function setUp()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Only valid for php >= 5.4');
        }
    }

    public function testGuardForArrayOrTraversableThrowsException()
    {
        $object = new GuardedObject;
        $this->setExpectedException(
            'Zend\Stdlib\Exception\InvalidArgumentException',
            'Argument must be an array or Traversable, [string] given'
        );
        $object->setArrayOrTraversable('');
    }

    public function testGuardForArrayOrTraversableAllowsArray()
    {
        $object = new GuardedObject;
        $this->assertNull($object->setArrayOrTraversable(array()));
    }

    public function testGuardForArrayOrTraversableAllowsTraversable()
    {
        $object      = new GuardedObject;
        $traversable = new ArrayObject;
        $this->assertNull($object->setArrayOrTraversable($traversable));
    }

}
