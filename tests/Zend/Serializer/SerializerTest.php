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
 * @package    Zend_Serializer
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Serializer;

use Zend\Serializer\Serializer,
    Zend\Loader\PluginLoader,
    Zend\Serializer\Adapter;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SerializerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
        Serializer::resetAdapterLoader();
    }

    public function testGetDefaultAdapterLoader()
    {
        $this->assertTrue(Serializer::getAdapterLoader() instanceof PluginLoader);
    }

    public function testChangeAdapterLoader()
    {
        $newLoader = new PluginLoader();
        Serializer::setAdapterLoader($newLoader);
        $this->assertTrue(Serializer::getAdapterLoader() === $newLoader);
    }

    public function testDefaultAdapter()
    {
        $adapter = Serializer::getDefaultAdapter();
        $this->assertTrue($adapter instanceof Adapter);
    }

    public function testFactoryValidCall()
    {
        $serializer = Serializer::factory('PhpSerialize');
        $this->assertTrue($serializer instanceof Adapter\PHPSerialize);
    }

    public function testFactoryUnknownAdapter()
    {
        $this->setExpectedException('Zend\\Serializer\\Exception','Can\'t load serializer adapter');
        Serializer::factory('unknown');
    }
    
    public function testFactoryOnADummyClassAdapter()
    {
        $this->setExpectedException('Zend\\Serializer\\Exception','must implement Zend\\Serializer\\Adapter');
        Serializer::setAdapterLoader(new PluginLoader(array('ZendTest\\Serializer\\TestAsset' => __DIR__ . '/TestAsset')));
        Serializer::factory('dummy');
    }

    public function testChangeDefaultAdapterWithString()
    {
        Serializer::setDefaultAdapter('Json');
        $this->assertTrue(Serializer::getDefaultAdapter() instanceof Adapter\Json);
    }

    public function testChangeDefaultAdapterWithInstance()
    {
        $newAdapter = new Adapter\PHPSerialize();

        Serializer::setDefaultAdapter($newAdapter);
        $this->assertTrue($newAdapter === Serializer::getDefaultAdapter());
    }

    public function testSerializeDefaultAdapter()
    {
        $value = 'test';
        $adapter = Serializer::getDefaultAdapter();
        $expected = $adapter->serialize($value);
        $this->assertEquals($expected, Serializer::serialize($value));
    }

    public function testSerializeSpecificAdapter()
    {
        $value = 'test';
        $adapter = new Adapter\Json();
        $expected = $adapter->serialize($value);
        $this->assertEquals($expected, Serializer::serialize($value, array('adapter' => $adapter)));
    }

    public function testUnserializeDefaultAdapter()
    {
        $value = 'test';
        $adapter = Serializer::getDefaultAdapter();
        $value = $adapter->serialize($value);
        $expected = $adapter->unserialize($value);
        $this->assertEquals($expected, Serializer::unserialize($value));
    }

    public function testUnserializeSpecificAdapter()
    {
        $adapter = new Adapter\Json();
        $value = '"test"';
        $expected = $adapter->unserialize($value);
        $this->assertEquals($expected, Serializer::unserialize($value, array('adapter' => $adapter)));
    }

}
