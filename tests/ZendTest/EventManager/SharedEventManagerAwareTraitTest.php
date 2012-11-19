<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_EventManager
 */

namespace ZendTest\EventManager;

use \PHPUnit_Framework_TestCase as TestCase;
use \Zend\EventManager\SharedEventManager;

/**
 * @requires PHP 5.4
 */
class SharedEventManagerAwareTraitTest extends TestCase
{
    public function testSetSharedManager()
    {
        $object = $this->getObjectForTrait('\Zend\EventManager\SharedEventManagerAwareTrait');

        $this->assertAttributeEquals(null, 'sharedEventManager', $object);

        $sharedEventManager = new SharedEventManager;

        $object->setSharedManager($sharedEventManager);

        $this->assertAttributeEquals($sharedEventManager, 'sharedEventManager', $object);
    }

    public function testGetSharedManager()
    {
        $object = $this->getObjectForTrait('\Zend\EventManager\SharedEventManagerAwareTrait');

        $this->assertEquals(null, $object->getSharedManager());

        $sharedEventManager = new SharedEventManager;

        $object->setSharedManager($sharedEventManager);

        $this->assertEquals($sharedEventManager, $object->getSharedManager());
    }

    public function testUnsetSharedManager()
    {
        $object = $this->getObjectForTrait('\Zend\EventManager\SharedEventManagerAwareTrait');

        $this->assertAttributeEquals(null, 'sharedEventManager', $object);

        $sharedEventManager = new SharedEventManager;

        $object->setSharedManager($sharedEventManager);

        $this->assertAttributeEquals($sharedEventManager, 'sharedEventManager', $object);

        $object->unsetSharedManager();

        $this->assertAttributeEquals(null, 'sharedEventManager', $object);
    }
}
