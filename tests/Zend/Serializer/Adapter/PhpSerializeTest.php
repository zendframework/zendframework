<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */

/** @see Zend_Serializer_Adapter_PhpSerialize */
require_once 'Zend/Serializer/Adapter/PhpSerialize.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Serializer
 * @subpackage UnitTests
 */
class Zend_Serializer_Adapter_PhpSerializeTest extends PHPUnit_Framework_TestCase
{

    private $_adapter;

    public function setUp()
    {
        $this->_adapter = new Zend_Serializer_Adapter_PhpSerialize();
    }

    public function tearDown()
    {
        $this->_adapter = null;
    }

    public function testSerializeString() {
        $value      = 'test';
        $expected   = 's:4:"test";';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse() {
        $value    = false;
        $expected = 'b:0;';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNull() {
        $value    = null;
        $expected = 'N;';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNumeric() {
        $value    = 100;
        $expected = 'i:100;';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObject() {
        $value    = new stdClass();
        $expected = 'O:8:"stdClass":0:{}';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeString() {
        $value    = 's:4:"test";';
        $expected = 'test';

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeFalse() {
        $value    = 'b:0;';
        $expected = false;

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNull() {
        $value    = 'N;';
        $expected = null;

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNumeric() {
        $value    = 'i:100;';
        $expected = 100;

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeObject() {
        $value    = 'O:8:"stdClass":0:{}';
        $expected = new stdClass();

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserialzeInvalid() {
        $value = 'not a serialized string';
        $this->setExpectedException('Zend_Serializer_Exception');
        $this->_adapter->unserialize($value);
    }

}
