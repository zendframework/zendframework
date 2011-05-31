<?php
namespace ZendTest\Di;

use Zend\Di\DependencyInjectionContainer,
    Zend\Di\DependencyInjector,
    Zend\Di\Definition;
    
use PHPUnit_Framework_TestCase as TestCase;

class DependencyInjectionContainerTest extends TestCase
{
    public function setUp()
    {
        $this->di = new DependencyInjectionContainer();
    }

    public function testComposesDependencyInjectorInstanceByDefault()
    {
        $this->assertInstanceOf('Zend\Di\DependencyInjector', $this->di->getInjector());
    }

    public function testCanComposeInCustomDiInstance()
    {
        $di = new DependencyInjector();
        $this->di->setInjector($di);
        $this->assertSame($di, $this->di->getInjector());
    }

    public function testGetFallsBacktoInjectorWhenUnableToFindService()
    {
        $di = $this->di->getInjector();
        $def = new Definition('ZendTest\Di\TestAsset\Struct');
        $def->setParam('param1', 'foo')
            ->setParam('param2', 'bar');
        $di->setDefinition($def, 'struct');

        $test = $this->di->get('struct');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\Struct', $test);
        $this->assertEquals('foo', $test->param1);
        $this->assertEquals('bar', $test->param2);
    }

    public function testRegisteredServicesArePreferredOverInjectorProvidedServices()
    {
        $di = $this->di->getInjector();
        $def = new Definition('ZendTest\Di\TestAsset\Struct');
        $def->setParam('param1', 'foo')
            ->setParam('param2', 'bar');
        $di->setDefinition($def, 'struct');

        $explicit = new TestAsset\Struct('FOO', 'BAR');
        $this->di->set('struct', $explicit);

        $test = $this->di->get('struct');
        $this->assertSame($explicit, $test);
    }
}
