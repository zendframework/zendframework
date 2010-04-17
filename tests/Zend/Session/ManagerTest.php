<?php

namespace ZendTest\Session;

use Zend\Session\Manager,
    Zend\Session;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->manager = new Manager();
    }

    public function testManagerUsesSessionConfigurationByDefault()
    {
        $config = $this->manager->getConfig();
        $this->assertTrue($config instanceof Session\Configuration\SessionConfiguration);
    }

    public function testCanPassConfigurationToConstructor()
    {
        $config = new Session\Configuration\StandardConfiguration();
        $manager = new Manager($config);
        $this->assertSame($config, $manager->getConfig());
    }

    public function testPassingUnknownStringClassForConfigurationRaisesException()
    {
        $this->setExpectedException('Zend\\Session\\Exception', 'invalid');
        $manager = new Manager('foobarbazbat');
    }

    public function testPassingInvalidStringClassForConfigurationRaisesException()
    {
        $this->setExpectedException('Zend\\Session\\Exception', 'invalid');
        $manager = new Manager('Zend\\Session\\Storage\\ArrayStorage');
    }

    public function testPassingValidStringClassForConfigurationInstantiatesThatConfiguration()
    {
        $manager = new Manager('Zend\\Session\\Configuration\\StandardConfiguration');
        $config = $manager->getConfig();
        $this->assertTrue($config instanceof Session\Configuration\StandardConfiguration);
    }

    public function testPassingValidStringClassInClassKeyOfArrayConfigurationInstantiatesThatConfiguration()
    {
        $manager = new Manager(array('class' => 'Zend\\Session\\Configuration\\StandardConfiguration'));
        $config = $manager->getConfig();
        $this->assertTrue($config instanceof Session\Configuration\StandardConfiguration);
    }

    public function testPassingInvalidStringClassInClassKeyOfArrayConfigurationRaisesException()
    {
        $this->setExpectedException('Zend\\Session\\Exception', 'invalid');
        $manager = new Manager(array('class' => 'foobarbaz'));
    }

    public function testPassingValidStringClassInClassKeyOfArrayConfigurationInstantiatesThatConfigurationWithOptionsProvided()
    {
        $manager = new Manager(array(
            'class'     => 'Zend\\Session\\Configuration\\StandardConfiguration',
            'save_path' => __DIR__,
        ));
        $config = $manager->getConfig();
        $this->assertTrue($config instanceof Session\Configuration\StandardConfiguration);
        $this->assertEquals(__DIR__, $config->getSavePath());
    }

    public function testPassingZendConfigObjectForConfigurationInstantiatesThatConfiguration()
    {
        $config = new \Zend\Config\Config(array(
            'class'     => 'Zend\\Session\\Configuration\\StandardConfiguration',
            'save_path' => __DIR__,
        ));
        $manager = new Manager($config);
        $config = $manager->getConfig();
        $this->assertTrue($config instanceof Session\Configuration\StandardConfiguration);
        $this->assertEquals(__DIR__, $config->getSavePath());
    }

    public function testManagerUsesSessionHandlerByDefault()
    {
        $handler = $this->manager->getHandler();
        $this->assertTrue($handler instanceof Session\Handler\SessionHandler);
    }

    public function testCanPassHandlerToConstructor()
    {
        $handler = new Session\Handler\SessionHandler();
        $manager = new Manager(null, null, $handler);
        $this->assertSame($handler, $manager->getHandler());
    }

    public function testManagerUsesSessionStorageByDefault()
    {
        $storage = $this->manager->getStorage();
        $this->assertTrue($storage instanceof Session\Storage\SessionStorage);
    }

    public function testCanPassStorageToConstructor()
    {
        $storage = new Session\Storage\ArrayStorage();
        $manager = new Manager(null, $storage);
        $this->assertSame($storage, $manager->getStorage());
    }

    public function testManagerConfigurationIsInjectedIntoHandler()
    {
        $config  = $this->manager->getConfig();
        $handler = $this->manager->getHandler();
        $this->assertSame($config, $handler->getConfig());
    }

    public function testManagerStorageIsInjectedIntoHandler()
    {
        $storage = $this->manager->getStorage();
        $handler = $this->manager->getHandler();
        $this->assertSame($storage, $handler->getStorage());
    }

    public function testCanPassStringStorageNameToConstructor()
    {
        $manager = new Manager(null, 'Zend\\Session\\Storage\\ArrayStorage');
        $storage = $manager->getStorage();
        $this->assertTrue($storage instanceof Session\Storage\ArrayStorage);
    }

    public function testCanPassStringHandlerNameToConstructor()
    {
        $manager = new Manager(null, null, 'ZendTest\\Session\\TestAsset\\TestHandler');
        $handler = $manager->getHandler();
        $this->assertTrue($handler instanceof TestAsset\TestHandler);
    }

    public function testCanPassStorageClassToConfigurationOptions()
    {
        $manager = new Manager(array('storage' => 'Zend\\Session\\Storage\\ArrayStorage'));
        $storage = $manager->getStorage();
        $this->assertTrue($storage instanceof Session\Storage\ArrayStorage);
    }

    public function testCanPassHandlerClassToConfigurationOptions()
    {
        $manager = new Manager(array('handler' => 'ZendTest\\Session\\TestAsset\\TestHandler'));
        $handler = $manager->getHandler();
        $this->assertTrue($handler instanceof TestAsset\TestHandler);
    }

    public function testHandlerReceivesConfigurationAndStorageWhenPassingHandlerClassViaConfigurationOptions()
    {
        $manager = new Manager(array(
            'class'   => 'Zend\\Session\\Configuration\\StandardConfiguration',
            'storage' => 'Zend\\Session\\Storage\\ArrayStorage',
            'handler' => 'ZendTest\\Session\\TestAsset\\TestHandler',
        ));
        $config  = $manager->getConfig();
        $this->assertTrue($config instanceof Session\Configuration\StandardConfiguration);
        $storage = $manager->getStorage();
        $this->assertTrue($storage instanceof Session\Storage\ArrayStorage);
        $handler = $manager->getHandler();
        $this->assertTrue($handler instanceof TestAsset\TestHandler);
        $this->assertSame($config, $handler->getConfig());
        $this->assertSame($storage, $handler->getStorage());
    }

    public function testPassingStorageViaParamOverridesStorageInConfig()
    {
        $storage = new Session\Storage\ArrayStorage();
        $manager = new Manager(array(
            'class'   => 'Zend\\Session\\Configuration\\StandardConfiguration',
            'storage' => 'Zend\\Session\\Storage\\SessionStorage',
            'handler' => 'ZendTest\\Session\\TestAsset\\TestHandler',
        ), $storage);
        $this->assertSame($storage, $manager->getStorage());
    }

    public function testPassingHandlerViaParamOverridesHandlerInConfig()
    {
        $handler = new TestAsset\TestHandler();
        $manager = new Manager(array(
            'class'   => 'Zend\\Session\\Configuration\\StandardConfiguration',
            'storage' => 'Zend\\Session\\Storage\\SessionStorage',
            'handler' => 'Zend\\Session\\Handler\\SessionHandler',
        ), null, $handler);
        $this->assertSame($handler, $manager->getHandler());
    }
}
