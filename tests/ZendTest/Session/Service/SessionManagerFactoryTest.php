<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Session\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\Session\Storage\ArrayStorage;

/**
 * @group      Zend_Session
 */
class SessionManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->services = new ServiceManager();
        $this->services->setFactory('Zend\Session\ManagerInterface', 'Zend\Session\Service\SessionManagerFactory');
    }

    public function testCreatesSessionManager()
    {
        $manager = $this->services->get('Zend\Session\ManagerInterface');
        $this->assertInstanceOf('Zend\Session\SessionManager', $manager);
    }

    public function testConfigObjectIsInjectedIfPresentInServices()
    {
        $config = $this->getMock('Zend\Session\Config\ConfigInterface');
        $this->services->setService('Zend\Session\Config\ConfigInterface', $config);
        $manager = $this->services->get('Zend\Session\ManagerInterface');
        $test = $manager->getConfig();
        $this->assertSame($config, $test);
    }

    public function testFactoryWillInjectStorageIfPresentInServices()
    {
        // Using concrete version here as mocking was too complex
        $storage = new ArrayStorage();
        $this->services->setService('Zend\Session\Storage\StorageInterface', $storage);
        $manager = $this->services->get('Zend\Session\ManagerInterface');
        $test = $manager->getStorage();
        $this->assertSame($storage, $test);
    }

    public function testFactoryWillInjectSaveHandlerIfPresentInServices()
    {
        $saveHandler = $this->getMock('Zend\Session\SaveHandler\SaveHandlerInterface');
        $this->services->setService('Zend\Session\SaveHandler\SaveHandlerInterface', $saveHandler);
        $manager = $this->services->get('Zend\Session\ManagerInterface');
        $test = $manager->getSaveHandler();
        $this->assertSame($saveHandler, $test);
    }

    public function testFactoryWillMarkManagerAsContainerDefaultByDefault()
    {
        $manager = $this->services->get('Zend\Session\ManagerInterface');
        $this->assertSame($manager, Container::getDefaultManager());
    }

    public function testCanDisableContainerDefaultManagerInjectionViaConfiguration()
    {
        $config = array('session_manager' => array(
            'enable_default_container_manager' => false,
        ));
        $this->services->setService('Config', $config);
        $manager = $this->services->get('Zend\Session\ManagerInterface');
        $this->assertNotSame($manager, Container::getDefaultManager());
    }

    /**
     * @runInSeparateProcess
     */
    public function testFactoryWillAddValidatorViaConfiguration()
    {
        $config = array('session_manager' => array(
            'validators' => array(
                'Zend\Session\Validator\RemoteAddr',
            ),
        ));
        $this->services->setService('Config', $config);
        $manager = $this->services->get('Zend\Session\ManagerInterface');

        $manager->start();

        $this->assertEquals(1, $manager->getValidatorChain()->getListeners('session.validate')->count());
    }

    /**
     * @runInSeparateProcess
     */
    public function testStartingSessionManagerFromFactoryDoesNotTriggerUndefinedVariable()
    {
        $storage = new ArrayStorage();
        $this->services->setService('Zend\Session\Storage\StorageInterface', $storage);

        $manager = $this->services->get('Zend\Session\ManagerInterface');
        $manager->start();

        $this->assertSame($storage, $manager->getStorage());
    }

    /**
     * @runInSeparateProcess
     */
    public function testFactoryDoesNotOverwriteValidatorStorageValues()
    {
        $storage = new ArrayStorage();
        $storage->setMetadata('_VALID', array(
            'Zend\Session\Validator\HttpUserAgent' => 'Foo',
            'Zend\Session\Validator\RemoteAddr'    => '1.2.3.4',
        ));
        $this->services->setService('Zend\Session\Storage\StorageInterface', $storage);

        $config = array(
            'session_manager' => array(
                'validators' => array(
                    'Zend\Session\Validator\HttpUserAgent',
                    'Zend\Session\Validator\RemoteAddr',
                ),
            ),
        );
        $this->services->setService('Config', $config);

        // This call is needed to make sure session storage data is not overwritten by the factory
        $manager = $this->services->get('Zend\Session\ManagerInterface');

        $validatorData = $storage->getMetaData('_VALID');
        $this->assertSame('Foo', $validatorData['Zend\Session\Validator\HttpUserAgent']);
        $this->assertSame('1.2.3.4', $validatorData['Zend\Session\Validator\RemoteAddr']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testFactoryDoesNotAttachValidatorTwoTimes()
    {
        $storage = new ArrayStorage();
        $storage->setMetadata('_VALID', array(
            'Zend\Session\Validator\RemoteAddr' => '1.2.3.4',
        ));
        $this->services->setService('Zend\Session\Storage\StorageInterface', $storage);

        $config = array(
            'session_manager' => array(
                'validators' => array(
                    'Zend\Session\Validator\RemoteAddr',
                ),
            ),
        );
        $this->services->setService('Config', $config);

        $manager = $this->services->get('Zend\Session\ManagerInterface');
        try {
            $manager->start();
        } catch (\RuntimeException $e) {
            // Ignore exception, because we are not interested whether session validation passes in this test
        }

        $this->assertEquals(1, $manager->getValidatorChain()->getListeners('session.validate')->count());
    }
}
