<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */

namespace ZendTest\Di;

use Zend\Di\Configuration;
use Zend\Di\Di;
use Zend\Config\Factory as ConfigFactory;
use PHPUnit_Framework_TestCase as TestCase;

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

    public function testConfigurationCanConfigureRuntimeDefinitionDefaultFromIni()
    {
        $ini = ConfigFactory::fromFile(__DIR__ . '/_files/sample.ini', true)->get('section-c');
        $config = new Configuration($ini->di);
        $di = new Di();
        $di->configure($config);
        $definition = $di->definitions()->getDefinitionByType('Zend\Di\Definition\RuntimeDefinition');
        $this->assertInstanceOf('Zend\Di\Definition\RuntimeDefinition', $definition);
        $this->assertFalse($definition->getIntrospectionStrategy()->getUseAnnotations());
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

    public function testConfigurationCanConfigureRuntimeDefinitionUseAnnotationFromIni()
    {
        $ini = ConfigFactory::fromFile(__DIR__ . '/_files/sample.ini', true)->get('section-e');
        $config = new Configuration($ini->di);
        $di = new Di();
        $di->configure($config);
        $definition = $di->definitions()->getDefinitionByType('Zend\Di\Definition\RuntimeDefinition');
        $this->assertTrue($definition->getIntrospectionStrategy()->getUseAnnotations());
    }

    public function testConfigurationCanConfigureCompiledDefinition()
    {
        $config = ConfigFactory::fromFile(__DIR__ . '/_files/sample.php', true);
        $config = new Configuration($config->di);
        $di = new Di();
        $di->configure($config);
        $definition = $di->definitions()->getDefinitionByType('Zend\Di\Definition\ArrayDefinition');
        $this->assertInstanceOf('Zend\Di\Definition\ArrayDefinition', $definition);
        $this->assertTrue($di->definitions()->hasClass('My\DbAdapter'));
        $this->assertTrue($di->definitions()->hasClass('My\EntityA'));
        $this->assertTrue($di->definitions()->hasClass('My\Mapper'));
        $this->assertTrue($di->definitions()->hasClass('My\RepositoryA'));
        $this->assertTrue($di->definitions()->hasClass('My\RepositoryB'));
        $this->assertFalse($di->definitions()->hasClass('My\Foo'));
    }
}
