<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */

/** @see Zend_Serializer_Adapter_Igbinary */
require_once 'Zend/Serializer/Adapter/Igbinary.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Serializer
 * @subpackage UnitTests
 */
class Zend_Serializer_Adapter_IgbinaryTest extends PHPUnit_Framework_TestCase
{

    private $_adapter;

    public function setUp()
    {
        $this->_adapter = new Zend_Serializer_Adapter_Igbinary();
    }

    public function tearDown()
    {
        $this->_adapter = null;
    }

    public function testSerializeString() {
        $value    = 'test';
        $expected = igbinary_serialize($value);

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse() {
        $value    = false;
        $expected = igbinary_serialize($value);

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNull() {
        $value    = null;
        $expected = igbinary_serialize($value);

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNumeric() {
        $value    = 100;
        $expected = igbinary_serialize($value);

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObject() {
        $value    = new stdClass();
        $expected = igbinary_serialize($value);

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeString() {
        $expected = 'test';
        $value    = igbinary_serialize($expected);

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeFalse() {
        $expected = false;
        $value    = igbinary_serialize($expected);

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNull() {
        $expected = null;
        $value    = igbinary_serialize($expected);

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNumeric() {
        $expected = 100;
        $value    = igbinary_serialize($expected);

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeObject() {
        $expected = new stdClass();
        $value    = igbinary_serialize($expected);

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserialzeInvalid() {
        $value = "\0\1\r\n";
        $this->setExpectedException('Zend_Serializer_Exception');
        $this->_adapter->unserialize($value);
    }

}
