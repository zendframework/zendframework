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
 * @package    Zend_Memory
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */

/** Zend_Memory */

/**
 * @category   Zend
 * @package    Zend_Memory
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Memory
 */
class Zend_Memory_Container_LockedTest extends PHPUnit_Framework_TestCase
{
    /**
     * tests the Movable memory container object creation
     */
    public function testCreation()
    {
        $memObject = new Zend_Memory_Container_Locked('0123456789');

        $this->assertTrue($memObject instanceof Zend_Memory_Container_Locked);
    }


    /**
     * tests the value access methods
     */
    public function testValueAccess()
    {
        $memObject = new Zend_Memory_Container_Locked('0123456789');

        // getRef() method
        $this->assertEquals($memObject->getRef(), '0123456789');

        $valueRef = &$memObject->getRef();
        $valueRef[3] = '_';
        $this->assertEquals($memObject->getRef(), '012_456789');

        // value property
        $this->assertEquals((string)$memObject->value, '012_456789');

        $memObject->value[7] = '_';
        $this->assertEquals((string)$memObject->value, '012_456_89');

        $memObject->value = 'another value';
        $this->assertEquals((string)$memObject->value, 'another value');
    }


    /**
     * tests lock()/unlock()/isLocked() functions
     */
    public function testLock()
    {
        $memObject = new Zend_Memory_Container_Locked('0123456789');

        // It's always locked
        $this->assertTrue((boolean)$memObject->isLocked());

        $memObject->lock();
        $this->assertTrue((boolean)$memObject->isLocked());

        $memObject->unlock();
        // It's always locked
        $this->assertTrue((boolean)$memObject->isLocked());
    }

    /**
     * tests the touch() method
     */
    public function testTouch()
    {
        $memObject = new Zend_Memory_Container_Locked('0123456789');

        $memObject->touch();

        // Nothing to check
    }
}
