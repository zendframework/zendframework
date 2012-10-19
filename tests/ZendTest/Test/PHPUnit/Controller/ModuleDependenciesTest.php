<?php

namespace ZendTest\Test\PHPUnit\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ModuleDependenciesTest extends AbstractHttpControllerTestCase
{
    public function testDependenciesModules()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/_files/application.config.with.dependencies.php'
        );
        $sm = $this->getApplicationServiceLocator();
        $this->assertEquals(true, $sm->has('FooObject'));
        $this->assertEquals(true, $sm->has('BarObject'));
        
        $this->assertModulesLoaded(array('Foo', 'Bar'));
        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertModulesLoaded(array('Foo', 'Bar', 'Unknow'));
    }
    
    public function testBadDependenciesModules()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/_files/application.config.with.dependencies.disabled.php'
        );
        $sm = $this->getApplicationServiceLocator();
        $this->assertEquals(false, $sm->has('FooObject'));
        $this->assertEquals(true, $sm->has('BarObject'));
        
        $this->assertNotModulesLoaded(array('Foo'));
        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');
        $this->assertNotModulesLoaded(array('Foo', 'Bar'));
    }
}
