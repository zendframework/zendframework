<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc_Router
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Mvc\Router;

use Zend\Mvc\Router\RouteBroker,
    PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Zend
 * @package    Zend_Mvc_Router
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Router
 */
class RouteBrokerTest extends TestCase
{
    public function testGetPluginsReturnsNothing()
    {
        $broker = new RouteBroker();
        $this->assertNull($broker->getPlugins());
    }
    
    public function testIsLoadedReturnsNothing()
    {
        $broker = new RouteBroker();
        $this->assertNull($broker->isLoaded('foo'));
    }
    
    public function testRegisterReturnsNothing()
    {
        $broker = new RouteBroker();
        $this->assertNull($broker->register('foo', 'bar'));
    }
    
    public function testUnregisterReturnsNothing()
    {
        $broker = new RouteBroker();
        $this->assertNull($broker->unregister('foo'));
    }
    
    public function testGetClassLoaderReturnsDefaultLoader()
    {
        $broker = new RouteBroker();
        $this->assertInstanceOf('Zend\Loader\PluginClassLoader', $broker->getClassLoader());
    }
    
    public function testSetClassLoader()
    {
        $broker = new RouteBroker();
        $loader = new \Zend\Loader\PluginClassLoader();
        $broker->setClassLoader($loader);
        
        $this->assertEquals($loader, $broker->getClassLoader());
    }
    
    public function testSetClassLoaderOnlyAllowsPluginClassLocator()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\InvalidArgumentException', 'Expected instance of PluginClassLocator');
        $broker = new RouteBroker();
        $broker->setClassLoader(new \Zend\Loader\PrefixPathLoader());
    }
    
    public function testLoadNonExistentRoute()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Unable to locate class associated with "foo"');
        $broker = new RouteBroker();
        $broker->load('foo');
    }
    
    public function testSetOptionsViaConstructor()
    {
        $loader = new \Zend\Loader\PluginClassLoader();
        $broker = new RouteBroker(array(
            'class_loader' => $loader
        ));
        
        $this->assertEquals($loader, $broker->getClassLoader());
    }
    
    public function testSetInvalidOptions()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\InvalidArgumentException', 'Expected an array or Traversable; received "string"');
        $broker = new RouteBroker();
        $broker->setOptions('foo');
    }
    
    public function testSetClassLoaderViaOptionsAsString()
    {
        $broker = new RouteBroker(array(
            'class_loader' => 'Zend\Loader\PluginClassLoader'
        ));
        
        $this->assertInstanceOf('Zend\Loader\PluginClassLoader', $broker->getClassLoader());
    }
    
    public function testSetClassLoaderViaOptionsAsStringWithInvalidClassname()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Unknown class "\Non\Existent\Class\Loader" provided as class loader option');
        $broker = new RouteBroker(array(
            'class_loader' => '\Non\Existent\Class\Loader'
        ));
    }
    
    public function testSetClassLoaderViaOptionsAsInteger()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'Option passed for class loader (integer) is of an unknown type');
        $broker = new RouteBroker(array(
            'class_loader' => 1
        ));
    }
    
    public function testSetClassLoaderViaOptionsAsArray()
    {
        $broker = new RouteBroker(array(
            'class_loader' => array(
                'class'   => 'Zend\Loader\PluginClassLoader',
                'options' => array(),
                'foo'     => 'bar'
            )
        ));
        
        $this->assertInstanceOf('Zend\Loader\PluginClassLoader', $broker->getClassLoader());
    }
    
    public function testIgnoreUnknownOptions()
    {
        $broker = new RouteBroker(array(
            'foo' => 'bar'
        ));
    }
}
