<?php

namespace ZendTest\Di;

use Zend\Di\InstanceManager;

class InstanceManagerTest extends \PHPUnit_Framework_TestCase
{
    
    public function testInstanceManagerImplementsInterface()
    {
        $im = new InstanceManager();
        $this->assertInstanceOf('Zend\Di\InstanceCollection', $im);
    }
    
    public function testInstanceManagerCanPersistInstances()
    {
        $im = new InstanceManager();
        $obj = new TestAsset\BasicClass();
        $im->addSharedInstance($obj, 'ZendTest\Di\TestAsset\BasicClass');
        $this->assertTrue($im->hasSharedInstance('ZendTest\Di\TestAsset\BasicClass'));
        $this->assertSame($obj, $im->getSharedInstance('ZendTest\Di\TestAsset\BasicClass')); 
    }
    
    public function testInstanceManagerCanPersistProperties()
    {
        $im = new InstanceManager();
        $im->setProperty('ZendTest\Di\TestAsset\BasicClass', 'foo', 'bar');
        $this->assertTrue($im->hasProperties('ZendTest\Di\TestAsset\BasicClass'));
        $this->assertTrue($im->hasProperty('ZendTest\Di\TestAsset\BasicClass', 'foo'));
        $this->assertEquals('bar', $im->getProperty('ZendTest\Di\TestAsset\BasicClass', 'foo')); 
    }
    
}