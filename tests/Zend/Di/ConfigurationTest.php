<?php

namespace ZendTest\Di;

use Zend\Di\Configuration,
    Zend\Di\Di,
    PHPUnit_Framework_TestCase as TestCase;

class ConfigurationTest extends TestCase
{
    public function testConfigurationCanConfigureInstanceManagerWithIniFile()
    {
        $ini = new \Zend\Config\Ini(__DIR__ . '/_files/sample.ini', 'section-a');
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
        $expected = array('parameters' => array('username' => 'readonly', 'password' => 'mypassword'), 'injections' => array());
        $this->assertEquals($expected, $im->getConfiguration('My\DbAdapter'));
        
        $this->assertTrue($im->hasConfiguration('my-dbAdapter'));
        $expected = array('parameters' => array('username' => 'readwrite'), 'injections' => array());
        $this->assertEquals($expected, $im->getConfiguration('my-dbAdapter'));
    }
    
    public function testConfigurationCanConfigureBuilderDefinitionFromIni()
    {
        $this->markTestIncomplete('Builder not updated to new DI yet');
        $ini = new \Zend\Config\Ini(__DIR__ . '/_files/sample.ini', 'section-b');
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
    
    public function testCanSetInstantiatorToStaticFactory()
    {
        $config = new Configuration(array(
            'definition' => array(
                'class' => array(
                    'ZendTest\Di\TestAsset\DummyParams' => array(
                        'instantiator' => array('ZendTest\Di\TestAsset\StaticFactory', 'factory'),
                    ),
                    'ZendTest\Di\TestAsset\StaticFactory' => array(
                        'methods' => array(
                            'factory' => array(
                                'struct' => array(
                                    'type' => 'ZendTest\Di\TestAsset\Struct',
                                    'required' => true,
                                ),
                                'params' => array(
                                    'required' => true,
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'instance' => array(
                'ZendTest\Di\TestAsset\DummyParams' => array(
                    'parameters' => array(
                        'struct' => 'ZendTest\Di\TestAsset\Struct',
                        'params' => array(
                            'foo' => 'bar',
                        ),
                    ),
                ),
                'ZendTest\Di\TestAsset\Struct' => array(
                    'parameters' => array(
                        'param1' => 'hello',
                        'param2' => 'world',
                    ),
                ),
            ),
        ));
        $di = new Di();
        $di->configure($config);
        $dummyParams = $di->get('ZendTest\Di\TestAsset\DummyParams');
        $this->assertEquals($dummyParams->params['param1'], 'hello');
        $this->assertEquals($dummyParams->params['param2'], 'world');
        $this->assertEquals($dummyParams->params['foo'], 'bar');
        $this->assertArrayNotHasKey('methods', $di->definitions()->hasMethods('ZendTest\Di\TestAsset\StaticFactory'));
    }
    
}
