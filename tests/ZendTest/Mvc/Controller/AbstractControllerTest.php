<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase;
use ReflectionProperty;
use Zend\Http\Response;

/**
 * @covers \Zend\Mvc\Controller\AbstractController
 */
class AbstractControllerTest extends TestCase
{
    /**
     * @var \Zend\Mvc\Controller\AbstractController|\PHPUnit_Framework_MockObject_MockObject
     */
    private $controller;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->controller = $this->getMockForAbstractClass('Zend\\Mvc\\Controller\\AbstractController');
    }

    /**
     * @group 6553
     */
    public function testSetEventManagerWithDefaultIdentifiers()
    {
        /* @var $eventManager \Zend\EventManager\EventManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
        $eventManager = $this->getMock('Zend\\EventManager\\EventManagerInterface');

        $eventManager->expects($this->once())->method('setIdentifiers')->with($this->countOf(4));

        $this->controller->setEventManager($eventManager);
    }

    /**
     * @group 6553
     */
    public function testSetEventManagerWithCustomStringIdentifier()
    {
        /* @var $eventManager \Zend\EventManager\EventManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
        $eventManager = $this->getMock('Zend\\EventManager\\EventManagerInterface');

        $eventManager->expects($this->once())->method('setIdentifiers')->with($this->logicalAnd(
                $this->countOf(5),
                $this->contains('customEventIdentifier')
            ));

        $reflection = new ReflectionProperty($this->controller, 'eventIdentifier');

        $reflection->setAccessible(true);
        $reflection->setValue($this->controller, 'customEventIdentifier');

        $this->controller->setEventManager($eventManager);
    }

    /**
     * @group 6553
     */
    public function testSetEventManagerWithMultipleCustomStringIdentifier()
    {
        /* @var $eventManager \Zend\EventManager\EventManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
        $eventManager = $this->getMock('Zend\\EventManager\\EventManagerInterface');

        $eventManager->expects($this->once())->method('setIdentifiers')->with($this->logicalAnd(
            $this->countOf(6),
            $this->contains('customEventIdentifier1'),
            $this->contains('customEventIdentifier2')
        ));

        $reflection = new ReflectionProperty($this->controller, 'eventIdentifier');

        $reflection->setAccessible(true);
        $reflection->setValue($this->controller, array('customEventIdentifier1', 'customEventIdentifier2'));

        $this->controller->setEventManager($eventManager);
    }
}
