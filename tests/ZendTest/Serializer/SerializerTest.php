<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Serializer
 */

namespace ZendTest\Serializer;

use Zend\Serializer\Adapter;
use Zend\Serializer\AdapterPluginManager;
use Zend\Serializer\Serializer;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage UnitTests
 * @group      Zend_Serializer
 */
class SerializerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
        Serializer::resetAdapterPluginManager();
    }

    public function testGetDefaultAdapterPluginManager()
    {
        $this->assertTrue(Serializer::getAdapterPluginManager() instanceof AdapterPluginManager);
    }

    public function testChangeAdapterPluginManager()
    {
        $newPluginManager = new AdapterPluginManager();
        Serializer::setAdapterPluginManager($newPluginManager);
        $this->assertTrue(Serializer::getAdapterPluginManager() === $newPluginManager);
    }

    public function testDefaultAdapter()
    {
        $adapter = Serializer::getDefaultAdapter();
        $this->assertTrue($adapter instanceof Adapter\AdapterInterface);
    }

    public function testFactoryValidCall()
    {
        $serializer = Serializer::factory('PhpSerialize');
        $this->assertTrue($serializer instanceof Adapter\PHPSerialize);
    }

    public function testFactoryUnknownAdapter()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        Serializer::factory('unknown');
    }

    public function testFactoryOnADummyClassAdapter()
    {
        $adapters = new AdapterPluginManager();
        $adapters->setInvokableClass('dummy', 'ZendTest\Serializer\TestAsset\Dummy');
        Serializer::setAdapterPluginManager($adapters);
        $this->setExpectedException('Zend\\Serializer\\Exception\\RuntimeException', 'AdapterInterface');
        Serializer::factory('dummy');
    }

    public function testChangeDefaultAdapterWithString()
    {
        Serializer::setDefaultAdapter('Json');
        $this->assertTrue(Serializer::getDefaultAdapter() instanceof Adapter\Json);
    }

    public function testChangeDefaultAdapterWithInstance()
    {
        $newAdapter = new Adapter\PhpSerialize();

        Serializer::setDefaultAdapter($newAdapter);
        $this->assertTrue($newAdapter === Serializer::getDefaultAdapter());
    }

    public function testFactoryPassesAdapterOptions()
    {
        $options = new Adapter\PythonPickleOptions(array('protocol' => 2));
        /** @var Adapter\PythonPickle $adapter  */
        $adapter = Serializer::factory('pythonpickle', $options);
        $this->assertTrue($adapter instanceof Adapter\PythonPickle);
        $this->assertEquals(2, $adapter->getOptions()->getProtocol());
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
        $this->assertEquals($expected, Serializer::serialize($value, $adapter));
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
        $this->assertEquals($expected, Serializer::unserialize($value, $adapter));
    }

}
