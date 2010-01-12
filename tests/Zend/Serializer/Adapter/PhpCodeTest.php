<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */

/** @see Zend_Serializer_Adapter_PhpCode */
require_once 'Zend/Serializer/Adapter/PhpCode.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Serializer
 * @subpackage UnitTests
 */
class Zend_Serializer_Adapter_PhpCodeTest extends PHPUnit_Framework_TestCase
{

    private $_adapter;

    public function setUp()
    {
        $this->_adapter = new Zend_Serializer_Adapter_PhpCode();
    }

    public function tearDown()
    {
        $this->_adapter = null;
    }

    public function testSerializeString() {
        $value      = 'test';
        $expected   = "'test'";

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
        $expected = 'NULL';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNumeric() {
        $value    = 100.12345;
        $expected = '100.12345';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObject() {
        $value    = new stdClass();
        $expected = "stdClass::__set_state(array(\n))";

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeString() {
        $value    = "'test'";
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
        $value    = 'NULL';
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

/* TODO: PHP Fatal error:  Call to undefined method stdClass::__set_state()
    public function testUnserializeObject() {
        $value    = "stdClass::__set_state(array(\n))";
        $expected = new stdClass();

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }
*/

    public function testUnserialzeInvalid() {
        $value = 'not a serialized string';
        $this->setExpectedException('Zend_Serializer_Exception');
        $this->_adapter->unserialize($value);
    }

}
