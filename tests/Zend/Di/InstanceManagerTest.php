<?php

namespace ZendTest\Di;

use Zend\Di\InstanceManager,
    PHPUnit_Framework_TestCase as TestCase;

class InstanceManagerTest extends TestCase
{
    
    public function testInstanceManagerCanPersistInstances()
    {
        $im = new InstanceManager();
        $obj = new TestAsset\BasicClass();
        $im->addSharedInstance($obj, 'ZendTest\Di\TestAsset\BasicClass');
        $this->assertTrue($im->hasSharedInstance('ZendTest\Di\TestAsset\BasicClass'));
        $this->assertSame($obj, $im->getSharedInstance('ZendTest\Di\TestAsset\BasicClass')); 
    }
    
    public function testInstanceManagerCanPersistInstancesWithParameters()
    {
        $im = new InstanceManager();
        $obj1 = new TestAsset\BasicClass();
        $obj2 = new TestAsset\BasicClass();
        $obj3 = new TestAsset\BasicClass();
        
        $im->addSharedInstance($obj1, 'foo');
        $im->addSharedInstanceWithParameters($obj2, 'foo', array('foo' => 'bar'));
        $im->addSharedInstanceWithParameters($obj3, 'foo', array('foo' => 'baz'));
        
        $this->assertSame($obj1, $im->getSharedInstance('foo'));
        $this->assertSame($obj2, $im->getSharedInstanceWithParameters('foo', array('foo' => 'bar')));
        $this->assertSame($obj3, $im->getSharedInstanceWithParameters('foo', array('foo' => 'baz')));
    }
    
    public function testInstanceManagerCanPersistParameters()
    {
        $this->markTestSkipped('Skipped');
        $im = new InstanceManager();
        $im->setProperty('ZendTest\Di\TestAsset\BasicClass', 'foo', 'bar');
        $this->assertTrue($im->hasProperties('ZendTest\Di\TestAsset\BasicClass'));
        $this->assertTrue($im->hasProperty('ZendTest\Di\TestAsset\BasicClass', 'foo'));
        $this->assertEquals('bar', $im->getProperty('ZendTest\Di\TestAsset\BasicClass', 'foo')); 
    }
    

    
}
