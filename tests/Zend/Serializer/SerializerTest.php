<?php
/**
 * @package    Zend_Serializer
 * @subpackage UnitTests
 */

/**
 * Zend_Serializer
 */
require_once 'Zend/Serializer.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Serializer
 * @subpackage UnitTests
 */
class Zend_Serializer_SerializerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Zend_Serializer::resetAdapterLoader();
    }

    public function tearDown()
    {
    }

    public function testGetDefaultAdapterLoader()
    {
        $this->assertTrue(Zend_Serializer::getAdapterLoader() instanceof Zend_Loader_PluginLoader);
    }

    public function testChangeAdapterLoader()
    {
        $newLoader = new Zend_Loader_PluginLoader();
        Zend_Serializer::setAdapterLoader($newLoader);
        $this->assertTrue(Zend_Serializer::getAdapterLoader() === $newLoader);
    }

    public function testFactoryValidCall()
    {
        $serializer = Zend_Serializer::factory('PhpSerialize');
        $this->assertTrue($serializer instanceof Zend_Serializer_Adapter_PhpSerialize);
    }

    public function testFactoryUnknownAdapter()
    {
        $this->setExpectedException('Zend_Serializer_Exception','Can\'t load serializer adapter');
        Zend_Serializer::factory('unknown');
    }
    
    public function testFactoryOnADummyClassAdapter()
    {
        $this->setExpectedException('Zend_Serializer_Exception','must implement Zend_Serializer_Adapter_AdapterInterface');
        Zend_Serializer::setAdapterLoader(new Zend_Loader_PluginLoader(array('Zend_Serializer_Adapter' => dirname(__FILE__).'/_files')));
        Zend_Serializer::factory('dummy');
    }

    public function testDefaultAdapter()
    {
        $adapter = Zend_Serializer::getDefaultAdapter();
        $this->assertTrue($adapter instanceof Zend_Serializer_Adapter_AdapterInterface);
    }

    public function testChangeDefaultAdapterWithString()
    {
        $newAdapter = 'Json';
        Zend_Serializer::setDefaultAdapter($newAdapter);
        $this->assertTrue(Zend_Serializer::getDefaultAdapter() instanceof Zend_Serializer_Adapter_Json);
    }

    public function testChangeDefaultAdapterWithInstance()
    {
        $newAdapter = new Zend_Serializer_Adapter_PhpSerialize();

        Zend_Serializer::setDefaultAdapter($newAdapter);
        $this->assertTrue($newAdapter === Zend_Serializer::getDefaultAdapter());
    }

    public function testSerializeDefaultAdapter()
    {
        $value = 'test';
        $adapter = Zend_Serializer::getDefaultAdapter();
        $expected = $adapter->serialize($value);
        $this->assertEquals($expected, Zend_Serializer::serialize($value));
    }

    public function testSerializeSpecificAdapter()
    {
        $value = 'test';
        $adapter = new Zend_Serializer_Adapter_Json();
        $expected = $adapter->serialize($value);
        $this->assertEquals($expected, Zend_Serializer::serialize($value, array('adapter' => $adapter)));
    }

    public function testUnserializeDefaultAdapter()
    {
        $value = 'test';
        $adapter = Zend_Serializer::getDefaultAdapter();
        $value = $adapter->serialize($value);
        $expected = $adapter->unserialize($value);
        $this->assertEquals($expected, Zend_Serializer::unserialize($value));
    }

    public function testUnserializeSpecificAdapter()
    {
        $adapter = new Zend_Serializer_Adapter_Json();
        $value = '"test"';
        $expected = $adapter->unserialize($value);
        $this->assertEquals($expected, Zend_Serializer::unserialize($value, array('adapter' => $adapter)));
    }

}
