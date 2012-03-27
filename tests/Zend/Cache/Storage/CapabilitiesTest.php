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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cache\Storage;

use Zend\Cache\Storage\Capabilities;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class CapabilitiesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Capabilities instance
     *
     * @var Zend\Cache\Storage\Capabilities
     */
    protected $_capabilities;

    /**
     * Base capabilities instance
     *
     * @var Zend\Cache\Storage\Capabilities
     */
    protected $_baseCapabilities;

    /**
     * Set/Change marker
     *
     * @var \stdClass
     */
    protected $_marker;

    public function setUp()
    {
        $this->_marker = new \stdClass();

        $this->_baseCapabilities = new Capabilities($this->_marker);

        $this->_capabilities = new Capabilities($this->_marker, array(), $this->_baseCapabilities);
    }

    public function testSetAndGetCapability()
    {
        $this->_capabilities->setMaxTtl($this->_marker, 100);
        $this->assertEquals(100, $this->_capabilities->getMaxTtl());
    }

    public function testGetCapabilityByBaseCapabilities()
    {
        $this->_baseCapabilities->setMaxTtl($this->_marker, 100);
        $this->assertEquals(100, $this->_capabilities->getMaxTtl());
    }

    public function testTriggerChangeEvent()
    {
        $em = $this->_capabilities->getEventManager();
        $this->assertInstanceOf('Zend\EventManager\EventManager', $em);

        $event = null;
        $em->attach('change', function ($eventArg) use (&$event) {
            $event = $eventArg;
        });

        $this->_capabilities->setMaxTtl($this->_marker, 100);

        $this->assertInstanceOf('Zend\EventManager\Event', $event);
        $this->assertEquals('change', $event->getName());
        $this->assertSame($this->_capabilities, $event->getTarget());
        $this->assertEquals(array('maxTtl' => 100), $event->getParams());
    }

    public function testTriggerChangeEventOfBaseCapabilities()
    {
        $em = $this->_capabilities->getEventManager();
        $this->assertInstanceOf('Zend\EventManager\EventManager', $em);

        $event = null;
        $em->attach('change', function ($eventArg) use (&$event) {
            $event = $eventArg;
        });

        $this->_baseCapabilities->setMaxTtl($this->_marker, 100);

        $this->assertInstanceOf('Zend\EventManager\Event', $event);
        $this->assertEquals('change', $event->getName());
        $this->assertSame($this->_baseCapabilities, $event->getTarget());
        $this->assertEquals(array('maxTtl' => 100), $event->getParams());
    }

}
