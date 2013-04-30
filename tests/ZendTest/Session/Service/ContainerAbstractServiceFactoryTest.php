<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Session\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\Session\Storage\ArrayStorage;

/**
 * @group      Zend_Session
 */
class ContainerAbstractServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public $config = array(
        'session_containers' => array(
            'foo',
            'bar',
            'Baz\Bat',
            'Underscore_Separated',
            'With\Digits_0123',
        ),
    );

    public function setUp()
    {
        $this->services = new ServiceManager();

        $this->services->setService('Zend\Session\Storage\StorageInterface', new ArrayStorage());
        $this->services->setFactory('Zend\Session\ManagerInterface', 'Zend\Session\Service\SessionManagerFactory');
        $this->services->addAbstractFactory('Zend\Session\Service\ContainerAbstractServiceFactory');

        $this->services->setService('Config', $this->config);
    }

    public function validContainers()
    {
        $containers = array();
        $config     = $this->config;
        foreach ($config['session_containers'] as $name) {
            $containers[] = array($name, $name);
        }

        return $containers;
    }

    /**
     * @dataProvider validContainers
     */
    public function testCanRetrieveNamedContainers($serviceName, $containerName)
    {
        $this->assertTrue($this->services->has($serviceName), "Container does not have service by name '$serviceName'");
        $container = $this->services->get($serviceName);
        $this->assertInstanceOf('Zend\Session\Container', $container);
        $this->assertEquals($containerName, $container->getName());
    }

    /**
     * @dataProvider validContainers
     */
    public function testContainersAreInjectedWithSessionManagerService($serviceName, $containerName)
    {
        $this->assertTrue($this->services->has($serviceName), "Container does not have service by name '$serviceName'");
        $container = $this->services->get($serviceName);
        $this->assertSame($this->services->get('Zend\Session\ManagerInterface'), $container->getManager());
    }

    public function invalidContainers()
    {
        $containers = array();
        $config = $this->config;
        foreach ($config['session_containers'] as $name) {
            $containers[] = array('SomePrefix\\' . $name);
        }
        $containers[] = array('DOES_NOT_EXIST');

        return $containers;
    }

    /**
     * @dataProvider invalidContainers
     */
    public function testInvalidContainerNamesAreNotMatchedByAbstractFactory($name)
    {
        $this->assertFalse($this->services->has($name));
    }
}
