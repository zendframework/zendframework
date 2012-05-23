<?php

namespace ZendTest\Di;

use Zend\Di\Configuration,
    Zend\Di\Di,
    Zend\Config\Factory as ConfigFactory,
    PHPUnit_Framework_TestCase as TestCase;

class ConfigurationTest extends TestCase
{
    public function testConfigurationCanConfigureInstanceManagerWithIniFile()
    {
        $ini = ConfigFactory::fromFile(__DIR__ . '/_files/sample.ini', true)->get('section-a');
        $config = new Configuration($ini->di);
        $di = new Di();
        $di->configure($config);

        $im = $di->instanceManager();
        
        $this->assertTrue($im->hasAlias('my-repository'));
        $this->assertEquals('My\RepositoryA', $im->getClassFromAlias('my-repository'));
        
        $this->assertTrue($im->hasAlias('my-mapper'));
        $this->assertEquals('My\Mapper', $im->getClassFromAlias('my-mapper'));
        
        $this->assertTrue($im->hasAlias('my-dbAdapter'));
        $this->assertEquals('My\DbAdapter', $im->getClassFromAlias('my-dbAdapter'));
        
        $this->assertTrue($im->hasTypePreferences('my-repository'));
        $this->assertContains('my-mapper', $im->getTypePreferences('my-repository'));
        
        $this->assertTrue($im->hasTypePreferences('my-mapper'));
        $this->assertContains('my-dbAdapter', $im->getTypePreferences('my-mapper'));

        $this->assertTrue($im->hasConfiguration('My\DbAdapter'));
        $expected = array('parameters' => array('username' => 'readonly', 'password' => 'mypassword'), 'injections' => array(), 'shared' => true);
        $this->assertEquals($expected, $im->getConfiguration('My\DbAdapter'));
        
        $this->assertTrue($im->hasConfiguration('my-dbAdapter'));
        $expected = array('parameters' => array('username' => 'readwrite'), 'injections' => array(), 'shared' => true);
        $this->assertEquals($expected, $im->getConfiguration('my-dbAdapter'));
    }
    
    public function testConfigurationCanConfigureBuilderDefinitionFromIni()
    {
        $this->markTestIncomplete('Builder not updated to new DI yet');
        $ini = ConfigFactory::fromFile(__DIR__ . '/_files/sample.ini', true)->get('section-b');
        $config = new Configuration($ini->di);
        $di = new Di($config);
        $definition = $di->getDefinition();
        
        $this->assertTrue($definition->hasClass('My\DbAdapter'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\DbAdapter'));
        $this->assertEquals(
            array('username' => null, 'password' => null),
            $definition->getInjectionMethodParameters('My\DbAdapter', '__construct')
            );
        
        $this->assertTrue($definition->hasClass('My\Mapper'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\Mapper'));
        $this->assertEquals(
            array('dbAdapter' => 'My\DbAdapter'),
            $definition->getInjectionMethodParameters('My\Mapper', '__construct')
            );
        
        $this->assertTrue($definition->hasClass('My\Repository'));
        $this->assertEquals('__construct', $definition->getInstantiator('My\Repository'));
        $this->assertEquals(
            array('mapper' => 'My\Mapper'),
            $definition->getInjectionMethodParameters('My\Repository', '__construct')
            );
        
    }

    public function testConfigurationCanConfigureRuntimeDefinitionEnabledByDefaultFromIni()
    {
        $ini = ConfigFactory::fromFile(__DIR__ . '/_files/sample.ini', true)->get('section-c');
        $config = new Configuration($ini->di);
        $di = new Di();
        $di->configure($config);
        $definition = $di->definitions()->getDefinitionByType('Zend\Di\Definition\RuntimeDefinition');
        $this->assertInstanceOf('Zend\Di\Definition\RuntimeDefinition', $definition);
    }

    public function testConfigurationCanConfigureRuntimeDefinitionDisabledFromIni()
    {
        $ini = ConfigFactory::fromFile(__DIR__ . '/_files/sample.ini', true)->get('section-d');
        $config = new Configuration($ini->di);
        $di = new Di();
        $di->configure($config);
        $definition = $di->definitions()->getDefinitionByType('Zend\Di\Definition\RuntimeDefinition');
        $this->assertFalse($definition);
    }

}
