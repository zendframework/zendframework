<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager\Di;

use Zend\ServiceManager\Di\DiServiceInitializer;
use Zend\ServiceManager\Di\DiInstanceManagerProxy;

class DiServiceInitializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DiServiceInitializer
     */
    protected $diServiceInitializer = null;

    /**@#+
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockDi = null;
    protected $mockServiceLocator = null;
    protected $mockDiInstanceManagerProxy = null;
    protected $mockDiInstanceManager = null;
    /**@#-*/

    public function setup()
    {
        $this->mockDi = $this->getMock('Zend\Di\Di', array('injectDependencies'));
        $this->mockServiceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->mockDiInstanceManagerProxy = new DiInstanceManagerProxy(
            $this->mockDiInstanceManager = $this->getMock('Zend\Di\InstanceManager'),
            $this->mockServiceLocator
        );
        $this->diServiceInitializer = new DiServiceInitializer(
            $this->mockDi,
            $this->mockServiceLocator,
            $this->mockDiInstanceManagerProxy
        );

    }

    /**
     * @covers Zend\ServiceManager\Di\DiServiceInitializer::initialize
     */
    public function testInitialize()
    {
        $instance = new \stdClass();

        // test di is called with proper instance
        $this->mockDi->expects($this->once())->method('injectDependencies')->with($instance);

        $this->diServiceInitializer->initialize($instance, $this->mockServiceLocator);
    }

    /**
     * @covers Zend\ServiceManager\Di\DiServiceInitializer::initialize
     * @todo this needs to be moved into its own class
     */
    public function testProxyInstanceManagersStayInSync()
    {
        $instanceManager = new \Zend\Di\InstanceManager();
        $proxy = new DiInstanceManagerProxy($instanceManager, $this->mockServiceLocator);
        $instanceManager->addAlias('foo', 'bar');

        $this->assertEquals($instanceManager->getAliases(), $proxy->getAliases());
    }

}
