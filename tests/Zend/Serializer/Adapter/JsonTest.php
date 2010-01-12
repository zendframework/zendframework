<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */

/** @see Zend_Serializer_Adapter_Json */
require_once 'Zend/Serializer/Adapter/Json.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Serializer
 * @subpackage UnitTests
 */
class Zend_Serializer_Adapter_JsonTest extends PHPUnit_Framework_TestCase
{

    private $_adapter;

    public function setUp()
    {
        $this->_adapter = new Zend_Serializer_Adapter_Json();
    }

    public function tearDown()
    {
        $this->_adapter = null;
    }

    public function testSerializeString() {
        $value    = 'test';
        $expected = '"test"';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse() {
        $value    = false;
        $expected = 'false';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNull() {
        $value    = null;
        $expected = 'null';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNumeric() {
        $value    = 100;
        $expected = '100';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObject() {
        $value       = new stdClass();
        $value->test = "test";
        $expected    = '{"test":"test"}';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeString() {
        $value    = '"test"';
        $expected = 'test';

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeFalse() {
        $value    = 'false';
        $expected = false;

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNull() {
        $value    = 'null';
        $expected = null;

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNumeric() {
        $value    = '100';
        $expected = 100;

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeAsArray() {
        $value    = '{"test":"test"}';
        $expected = array('test' => 'test');

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeAsObject() {
        $value      = '{"test":"test"}';
        $expected   = new stdClass();
        $expected->test = 'test';

        $data = $this->_adapter->unserialize($value, array('objectDecodeType' => Zend_Json::TYPE_OBJECT));
        $this->assertEquals($expected, $data);
    }

    public function testUnserialzeInvalid() {
        $value = 'not a serialized string';
        $this->setExpectedException('Zend_Serializer_Exception');
        $this->_adapter->unserialize($value);
    }

}
