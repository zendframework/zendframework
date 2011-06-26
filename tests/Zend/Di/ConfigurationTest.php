<?php

namespace ZendTest\Di;

use Zend\Di\Configuration,
    Zend\Di\DependencyInjector,
    PHPUnit_Framework_TestCase as TestCase;

class ConfigurationTest extends TestCase
{
    public function testConfigurationCanConfigureInstanceManagerWithIniFile()
    {
        $ini = new \Zend\Config\Ini(__DIR__ . '/_files/sample.ini', 'section-a');
        $config = new Configuration($ini->di);
        $di = new DependencyInjector($config);
        
        $im = $di->getInstanceManager();
        
        $this->assertTrue($im->hasAlias('my-repository'));
        $this->assertEquals('My\RepositoryA', $im->getClassFromAlias('my-repository'));
        
        $this->assertTrue($im->hasAlias('my-mapper'));
        $this->assertEquals('My\Mapper', $im->getClassFromAlias('my-mapper'));
        
        $this->assertTrue($im->hasAlias('my-dbAdapter'));
        $this->assertEquals('My\DbAdapter', $im->getClassFromAlias('my-dbAdapter'));
        
        $this->assertTrue($im->hasPreferredInstances('my-repository'));
        $this->assertContains('my-mapper', $im->getPreferredInstances('my-repository'));
        
        $this->assertTrue($im->hasPreferredInstances('my-mapper'));
        $this->assertContains('my-dbAdapter', $im->getPreferredInstances('my-mapper'));
        
        $this->assertTrue($im->hasProperty('My\DbAdapter', 'username'));
        $this->assertEquals('readonly', $im->getProperty('My\DbAdapter', 'username'));
        
        $this->assertTrue($im->hasProperty('My\DbAdapter', 'password'));
        $this->assertEquals('mypassword', $im->getProperty('My\DbAdapter', 'password'));
        
        $this->assertTrue($im->hasProperty('my-dbAdapter', 'username'));
        $this->assertEquals('readwrite', $im->getProperty('my-dbAdapter', 'username'));
        
    }
    
    public function testConfigurationCanConfigureBuilderDefinitionFromIni()
    {
        $ini = new \Zend\Config\Ini(__DIR__ . '/_files/sample.ini', 'section-b');
        $config = new Configuration($ini->di);
        $di = new DependencyInjector($config);
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
    
    
}
